<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="whg-wrapper">
    <div class="container direct-action-webgrade">
        <div>
            <h2 class="header-text" id="whgMainTitle">Website Health Grader</h2>
            <p class="description-text" id="whgMainDescription">Enter a URL to get an instant SEO and health analysis.</p>
            <div class="input-group">
                <input type="url" id="whgUrlInput" placeholder="https://www.example.com" class="url-input" required>
                <button id="whgAnalyzeButton" class="analyze-button">Analyze Website</button>
            </div>
            <div id="whgLoading" class="loading-container" style="display:none;">
                <div class="simple-spinner"></div>
                <p id="whgLoadingText">Analyzing... Please wait.</p>
            </div>
            <div id="whgResultsContainer" class="results-container" style="display:none;">
                <h3 id="whgReportTitle" class="report-title">Analysis Report</h3>
                <div id="whgScoreDisplay" class="score-display"></div>
                <ul id="whgReportList" class="report-list"></ul>
                <div class="contact-section">
                    <p class="contact-header">Improve your score?</p>
                    <a id="whgContactButton" href="<?php echo esc_url( $contact_page_url ); ?>" class="contact-button">Contact Us</a>
                </div>
                <button id="whgClearButton" class="clear-button">New Analysis</button>
            </div>
            <div id="whgMessageBox" class="message-box" style="display:none;"></div>
        </div>
    </div>
</div>
<script>
(function () {
    var $ = function(id) { return document.getElementById(id); };
    var ajaxUrl = '<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>';

    $('whgAnalyzeButton').addEventListener('click', function() {
        var url = $('whgUrlInput').value.trim();
        if(!url) return;
        $('whgLoading').style.display = 'flex';
        $('whgResultsContainer').style.display = 'none';

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=website_health_grade&url=' + encodeURIComponent(url)
        })
        .then(res => res.json())
        .then(report => {
            $('whgLoading').style.display = 'none';
            if(report.status === 'success') {
                $('whgResultsContainer').style.display = 'block';
                $('whgScoreDisplay').textContent = 'Score: ' + report.score + '/100';
                $('whgReportList').innerHTML = report.checks.map(c => `<li>${c.text}</li>`).join('');
            }
        });
    });

    $('whgClearButton').addEventListener('click', function() {
        $('whgResultsContainer').style.display = 'none';
        $('whgUrlInput').value = '';
    });
})();
</script>