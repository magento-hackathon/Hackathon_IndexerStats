IndexerStats = window.IndexerStats || {};
IndexerStats.Progress = Class.create();
IndexerStats.Progress.prototype = {
    initialize : function(progressElement) {
        this.progressElement = progressElement;
        this.progressBarElement = progressElement.select('span')[0];
        this.timeOffset = progressElement.dataset.now - (Date.now() / 1000);
        this.startTime = progressElement.dataset.started;
        this.estimatedEndTime = progressElement.dataset.estimated_end;
        setInterval(this.updateProgressbar.bind(this), 1000);
    },
    updateProgressbar : function() {
        var percentDone = 100 * Math.min(1, (Date.now() / 1000 + this.timeOffset - this.startTime) / (this.estimatedEndTime - this.startTime));
        this.progressBarElement.style.width = percentDone + '%';
    }
};

document.observe("dom:loaded", function() {
    $$('.hackathon_indexerstats_progress').each(function (progressbar) {
        new IndexerStats.Progress(progressbar);
    });
});