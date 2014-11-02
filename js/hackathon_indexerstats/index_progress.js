IndexerStats = window.IndexerStats || {};

IndexerStats.AjaxRequest = Class.create();
IndexerStats.AjaxRequest.prototype = {
    initialize : function(link) {
        if (!link) {
            return;
        }
        var progressbar = $(link.parentNode.parentNode).select('.hackathon_indexerstats_info')[0];
        progressbar.addClassName('hackathon_indexerstats_progress');
        progressbar.parentNode.removeClassName('hackathon_indexerstats_finished');
        progressbar.progress = new IndexerStats.Progress(progressbar);

        new Ajax.Request(link.href, {
            onFailure: this.onFailure.bind(this),
            onSuccess: this.onSuccess.bind(this)
        }); 
        IndexerStats.status.update();
    },
    onSuccess : function(transport) {
        this.showMessage(transport.responseJSON.error ? 'error' : 'success', transport.responseJSON.message);
        IndexerStats.status.update();
    },
    onFailure : function(transport) {
        this.showMessage('error', 'Reindex request failed.');
        IndexerStats.status.update();
    },
    onMassComplete : function(grid, massaction, transport) {
        if (200 == transport.status) {
            this.onSuccess(transport);
        } else {
            this.onFailure(transport);
        }
    },
    showMessage : function(type, message)
    {
        $('messages').insert('<ul class="messages"><li class="' + type + '-msg"><ul><li><span>' + message + '</span></li></ul></li></ul>');
    }
};

IndexerStats.Status = Class.create();
IndexerStats.Status.prototype = {
    /*
     * minimum time between status update requests (in ms)
     */
    MIN_UPDATE_INTERVAL : 10000,
    /*
     * maximum time between status update requests (in ms)
     */
    MAX_UPDATE_INTERVAL : 60000,

    initialize : function() {
        this.isUpdating = false;
        this.timeoutId = null;
    },
    update : function() {
        if (this.isUpdating) return;
        this.isUpdating = true;
        new Ajax.Request('/admin/process/statusAjax', {
            loaderArea : false,
            onSuccess : this.onSuccess.bind(this),
            onFailure : this.onFailure.bind(this)
        });
    },
    onSuccess : function(transport) {
        this.isUpdating = false;
        var intervalToNextUpdate = this.MAX_UPDATE_INTERVAL;
        for (var i = 0, c = transport.responseJSON.process.length; i < c; ++i) {
            var processInfo = transport.responseJSON.process[i];
            var progressBar = $('hackathon_indexerstats_progress_' + processInfo.code);
            if (progressBar) {
                var timeColumn = progressBar.parentNode;
                var processTableRow = timeColumn.parentNode;
                var statusColumn = processTableRow.select('td')[4];
                var updateRequiredColumn = processTableRow.select('td')[5];
                var endedAtColumn = processTableRow.select('td')[6];

                if (endedAtColumn.innerHTML.trim() != processInfo.html_ended_at.trim()) {
                    // process has finished
                    progressBar.replace(processInfo.html_time);
                    statusColumn.update(processInfo.html_status);
                    updateRequiredColumn.update(processInfo.html_update_required);
                    endedAtColumn.update(processInfo.html_ended_at);
                    timeColumn.addClassName('hackathon_indexerstats_finished');
                    timeColumn.select('.hackathon_indexerstats_avgruntime')[0].update(
                        Translator.translate('Finished in') + ' ' + processInfo.last_running_time);
                } else if (processInfo.status == 'working') {
                    // process is running
                    progressBar.replace(processInfo.html_time)
                    statusColumn.update(processInfo.html_status);
                    // copy + paste von unten
                    $$('.hackathon_indexerstats_progress').each(function (progressbar) {
                        progressbar.progress = new IndexerStats.Progress(progressbar);
                    });
                    intervalToNextUpdate = Math.min(intervalToNextUpdate, progressbar.progress.estimatedEndTime . progressbar.progress.getCurrentTime());
                }
            }
        }
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }
        this.timeoutId = setTimeout(
            Math.max(this.MIN_UPDATE_INTERVAL, 1000 * intervalToNextUpdate),
            this.update.bind(this));
    },
    onFailure : function(transport) {
        this.isUpdating = false;
    }
};

IndexerStats.Progress = Class.create();
IndexerStats.Progress.prototype = {
    initialize : function(progressElement) {
        this.progressElement = progressElement;
        this.progressBarElement = progressElement.select('span')[0];
        this.timeDisplayElement = progressElement.select('.hackathon_indexerstats_time_display')[0];
        this.timeCaptionElement = progressElement.select('.hackathon_indexerstats_time_caption')[0];
        if (progressElement.dataset.started) {
            this.timeOffset = progressElement.dataset.now - (Date.now() / 1000);
            this.startTime = progressElement.dataset.started;
            this.estimatedEndTime = progressElement.dataset.estimated_end;
        } else {
            this.timeOffset = 0;
            this.startTime = Date.now() / 1000;
            this.estimatedEndTime = this.startTime + parseInt(progressElement.dataset.avg_runtime);
            this.timeCaptionElement.update(Translator.translate('remaining'));
            this.updateProgressbar();
        }
        setInterval(this.updateProgressbar.bind(this), 1000);
        this.shouldBeDone = false;
    },
    getCurrentTime : function() {
        return Date.now() / 1000 + this.timeOffset;
    },
    updateProgressbar : function() {
        var percentDone = 100 * Math.min(1, (this.getCurrentTime() - this.startTime) / (this.estimatedEndTime - this.startTime));
        if (percentDone == 100) {
            this.progressElement.removeClassName('hackathon_indexerstats_in_time');
            this.progressElement.addClassName('hackathon_indexerstats_not_in_time');
            this.timeCaptionElement.update(Translator.translate('over time'));
            if (!this.shouldBeDone) {
                this.shouldBeDone = true;
                IndexerStats.status.update();
            }
        }
        var remainingTime = Math.abs(this.estimatedEndTime - this.getCurrentTime());
        var remainingTimeDisplay = '';
        if (remainingTime > 86400) {
            remainingTimeDisplay += Math.floor(remainingTime / 86400) + 'd ';
        }
        if (remainingTime > 3600) {
            remainingTimeDisplay += Math.floor(remainingTime % 86400 / 3600) + 'h ';
        }
        if (remainingTime > 60) {
            remainingTimeDisplay += Math.floor(remainingTime % 3600 / 60) + 'm ';
        }
        remainingTimeDisplay += Math.floor(remainingTime % 60) + 's';
        this.timeDisplayElement.update(remainingTimeDisplay);
        this.progressBarElement.style.width = percentDone + '%';
    }
};

document.observe("dom:loaded", function() {
    $$('.hackathon_indexerstats_progress').each(function (progressbar) {
        progressbar.progress = new IndexerStats.Progress(progressbar);
    });
    indexer_processes_grid_massactionJsObject.apply = indexer_processes_grid_massactionJsObject.apply.wrap(
        function(parent) {
            if (this.select.value == 'reindex') {
                var firstProcessId = this.checkedString.split(',', 1);
                var progressbar = $('indexer_processes_grid_table')
                    .select('input[name=process][value=' + firstProcessId + ']')[0]
                    .parentNode.parentNode
                    .select('.hackathon_indexerstats_info')[0];
                progressbar.addClassName('hackathon_indexerstats_progress');
                progressbar.parentNode.removeClassName('hackathon_indexerstats_finished');
                progressbar.progress = new IndexerStats.Progress(progressbar);
            }
            parent();
        });
    IndexerStats.status = new IndexerStats.Status();
});