jQuery(document).ready(function($) {
    // Kontrast-Toggle Funktionalität
    $('.wcag-contrast-toggle').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var isActive = $button.hasClass('active');
        var $contrastStylesheet = $('#wcag-contrast-css');
        
        // Toggle active class
        $button.toggleClass('active');
        
        // Toggle body class
        $('body').toggleClass('wcag-contrast-active');
        
        // Lade oder entferne das CSS-File
        if (!isActive) {
            // Lade das Stylesheet, falls es noch nicht geladen ist
            if ($contrastStylesheet.length === 0) {
                $('head').append('<link id="wcag-contrast-css" rel="stylesheet" type="text/css" href="' + wcagSwitcher.pluginUrl + 'assets/css/wcag-contrast.css">');
            }
        } else {
            // Entferne das Stylesheet
            $contrastStylesheet.remove();
        }
        
        // Speichere den Status
        var newState = !isActive ? 'active' : 'inactive';
        localStorage.setItem('wcag_contrast_state', newState);
        
        // AJAX Request nur wenn wcagSwitcher verfügbar ist
        if (typeof wcagSwitcher !== 'undefined') {
            $.ajax({
                url: wcagSwitcher.ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_contrast_state',
                    state: newState,
                    nonce: wcagSwitcher.nonce
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Kontrast-Status gespeichert:', response.data.state);
                    }
                }
            });
        }
        
        // Debug Panel aktualisieren
        updateDebugPanel();
    });
    
    // Initialisiere den Status
    var savedState = localStorage.getItem('wcag_contrast_state');
    if (savedState === 'active') {
        $('.wcag-contrast-toggle').addClass('active');
        $('body').addClass('wcag-contrast-active');
        // Lade das CSS-File beim Start, wenn der Status aktiv ist
        if (typeof wcagSwitcher !== 'undefined') {
            $('head').append('<link id="wcag-contrast-css" rel="stylesheet" type="text/css" href="' + wcagSwitcher.pluginUrl + 'assets/css/wcag-contrast.css">');
        }
    }
    
    // Debug Panel Funktionalität
    if (typeof wcagSwitcher !== 'undefined' && wcagSwitcher.isAdmin) {
        $('.wcag-debug-toggle').on('click', function() {
            $('#wcag-debug-panel').toggle();
            updateDebugPanel();
        });
        
        $('.wcag-debug-reset').on('click', function() {
            localStorage.removeItem('wcag_contrast_state');
            $('.wcag-contrast-toggle').removeClass('active');
            $('body').removeClass('wcag-contrast-active');
            $('#wcag-contrast-css').remove();
            updateDebugPanel();
        });
    }
    
    // Debug Panel aktualisieren
    function updateDebugPanel() {
        if (typeof wcagSwitcher !== 'undefined' && wcagSwitcher.isAdmin) {
            var state = localStorage.getItem('wcag_contrast_state') || 'inactive';
            $('#wcag-debug-status').text(state === 'active' ? 'Aktiv' : 'Inaktiv');
            
            // LocalStorage Inhalt anzeigen
            var storageContent = {};
            for (var i = 0; i < localStorage.length; i++) {
                var key = localStorage.key(i);
                storageContent[key] = localStorage.getItem(key);
            }
            $('#wcag-debug-storage').text(JSON.stringify(storageContent, null, 2));
            
            // CSS-Datei Inhalt anzeigen
            $.get(wcagSwitcher.pluginUrl + 'assets/css/wcag-contrast.css', function(css) {
                $('#wcag-debug-css').text(css);
            });
        }
    }
}); 