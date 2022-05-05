var mainNavigationLinks=document.getElementById("main-navigation-links"),openMainNavigationMenuIcon=document.getElementById("open-main-navigation-menu-icon"),closeMainNavigationMenuIcon=document.getElementById("close-main-navigation-menu-icon"),themeToggleButton=document.getElementById("theme-toggle-button"),navigationToggleButton=document.getElementById("navigation-toggle-button"),sidebarToggleButton=document.getElementById("sidebar-toggle-button"),navigationOpen=!1,themeToggleButtons=document.querySelectorAll(".theme-toggle-button");function toggleNavigation(){(navigationOpen?hideNavigation:showNavigation)()}function showNavigation(){mainNavigationLinks.classList.remove("hidden"),openMainNavigationMenuIcon.style.display="none",closeMainNavigationMenuIcon.style.display="block",navigationOpen=!0}function hideNavigation(){mainNavigationLinks.classList.add("hidden"),openMainNavigationMenuIcon.style.display="block",closeMainNavigationMenuIcon.style.display="none",navigationOpen=!1}var sidebarOpen=768<=screen.width,sidebar=document.getElementById("documentation-sidebar"),backdrop=document.getElementById("sidebar-backdrop"),toggleButtons=document.querySelectorAll(".sidebar-button-wrapper");function toggleSidebar(){(sidebarOpen?hideSidebar:showSidebar)()}function showSidebar(){sidebar.classList.remove("hidden"),sidebar.classList.add("flex"),backdrop.classList.remove("hidden"),document.getElementById("app").style.overflow="hidden",toggleButtons.forEach(function(e){e.classList.remove("open"),e.classList.add("closed")}),sidebarOpen=!0}function hideSidebar(){sidebar.classList.add("hidden"),sidebar.classList.remove("flex"),backdrop.classList.add("hidden"),document.getElementById("app").style.overflow=null,toggleButtons.forEach(function(e){e.classList.add("open"),e.classList.remove("closed")}),sidebarOpen=!1}function toggleTheme(){"dark"===localStorage.getItem("color-theme")||!("color-theme"in localStorage)&&window.matchMedia("(prefers-color-scheme: dark)").matches?(document.documentElement.classList.remove("dark"),localStorage.setItem("color-theme","light")):(document.documentElement.classList.add("dark"),localStorage.setItem("color-theme","dark"))}themeToggleButtons.forEach(function(e){e.addEventListener("click",toggleTheme)}),navigationToggleButton&&(navigationToggleButton.onclick=toggleNavigation),sidebarToggleButton&&(sidebarToggleButton.onclick=toggleSidebar);