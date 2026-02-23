document.addEventListener("DOMContentLoaded", function() {
    // 1. Cek & Buka Tab Utama Terakhir
    var activeMainTab = localStorage.getItem('activeMainTab');
    if (activeMainTab) {
        var tabEl = document.querySelector(activeMainTab);
        if (tabEl) {
            var tab = new bootstrap.Tab(tabEl);
            tab.show();
        }
    }

    // 2. Cek & Buka Sub Tab (Pills) Terakhir
    var activePill = localStorage.getItem('activePill');
    if (activePill) {
        var pillEl = document.querySelector(activePill);
        if (pillEl) {
            var pill = new bootstrap.Tab(pillEl);
            pill.show();
        }
    }

    // 3. Simpan ID saat Tab Utama diklik
    var mainTabs = document.querySelectorAll('#mainTab .nav-link');
    mainTabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(e) {
            localStorage.setItem('activeMainTab', '#' + e.target.id);
        });
    });

    // 4. Simpan ID saat Sub Tab diklik
    var subPills = document.querySelectorAll('#pills-tab .nav-link');
    subPills.forEach(function(pill) {
        pill.addEventListener('shown.bs.tab', function(e) {
            localStorage.setItem('activePill', '#' + e.target.id);
        });
    });
});
