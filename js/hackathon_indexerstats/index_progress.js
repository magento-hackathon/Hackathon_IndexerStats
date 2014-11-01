IndexerStats = window.IndexerStats || {};

IndexerStats.AjaxRequest = Class.create();
IndexerStats.AjaxRequest.prototype = {
    initialize : function(url) {
        if (!url) {
            return;
        }
        new Ajax.Request(url, {
            onFailure: this.onFailure.bind(this),
            onSuccess: this.onSuccess.bind(this)
        }); 
    },
    onSuccess : function(transport) {
        this.showMessage(transport.responseJSON.error ? 'error' : 'success', transport.responseJSON.message);
    },
    onFailure : function(transport) {
    	this.showMessage('error', 'Reindex request failed.');
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

IndexerStats.Progress = Class.create();
IndexerStats.Progress.prototype = {
    initialize : function(progressElement) {
        this.progressElement = progressElement;
        this.progressBarElement = progressElement.select('span')[0];
        this.timeDisplayElement = progressElement.select('.hackathon_indexerstats_time_display')[0];
        this.timeCaptionElement = progressElement.select('.hackathon_indexerstats_time_caption')[0];
        this.timeOffset = progressElement.dataset.now - (Date.now() / 1000);
        this.startTime = progressElement.dataset.started;
        this.estimatedEndTime = progressElement.dataset.estimated_end;
        setInterval(this.updateProgressbar.bind(this), 1000);
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
        new IndexerStats.Progress(progressbar);
    });
});