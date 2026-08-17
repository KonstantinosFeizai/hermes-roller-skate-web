function showTab(event, tabId) {
  // 1. Hide all tab contents
  const tabs = document.querySelectorAll(".tab-content");
  tabs.forEach((tab) => {
    tab.classList.remove("active");
    tab.style.display = "none"; // Ensure it is hidden
  });

  // 2. Remove active state from the sidebar menu
  const menuItems = document.querySelectorAll(".sidebar-menu li");
  menuItems.forEach((item) => item.classList.remove("active"));

  // 3. Show the selected tab
  const activeTab = document.getElementById(tabId);
  activeTab.classList.add("active");
  activeTab.style.display = "block"; // Make it visible

  if (event && event.currentTarget) {
    event.currentTarget.classList.add("active");
  }

  // 4. Persist active tab in localStorage
  localStorage.setItem("activeTab", tabId);

  // 5. If athletes tab, run the region filter
  if (tabId === "athletes-tab") {
    // Use a short delay so the tab is rendered before filtering
    setTimeout(() => {
      filterByRegion(0);
    }, 50);
  }

  // 6. If classes tab, update lesson statuses (auto-complete past lessons)
  if (tabId === "classes-tab") {
    fetch(BASE_URL + "admin/update_lesson_statuses.php", { method: "POST" })
      .then((r) => r.json())
      .then((data) => {
        if (data.updated > 0) {
          location.reload();
        }
      })
      .catch(() => {});
  }

  // 7. If finance tab, auto-load data and start auto-refresh
  if (tabId === "finance-tab") {
    if (typeof refreshFinanceTab === "function") {
      refreshFinanceTab();
      if (typeof startFinanceAutoRefresh === "function") {
        startFinanceAutoRefresh();
      }
    }
  } else {
    // Stop auto-refresh when leaving finance tab
    if (typeof stopFinanceAutoRefresh === "function") {
      stopFinanceAutoRefresh();
    }
  }

  // 8. If messages tab, load sent messages history
  if (tabId === "messages-tab") {
    if (typeof loadSentMessages === "function") {
      loadSentMessages();
    }
  }
}
