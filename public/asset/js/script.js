const aaaleftBtn = document.querySelector('.l-v1-left-toggle');
const aaarightBtn = document.querySelector('.l-v1-right-toggle');
const aaaleftSidebar = document.getElementById('leftSidebar');
const aaarightSidebar = document.getElementById('rightSidebar');

document.addEventListener('DOMContentLoaded', function () {
    // 遍历所有有 submenu 的菜单项
    document.querySelectorAll('.l-v1-menu-item').forEach(item => {
        // 找子项中是否有 active
        const subItem = item.querySelector('.l-v1-submenu .active');

        // 如果有 active 的 submenu 子项，就展开 submenu
        if (subItem) {
            item.classList.add('open'); // 不要用 toggle，会意外移除
            const submenu = item.querySelector('.l-v1-submenu');
            if (submenu) submenu.style.display = 'block'; // 显示 submenu
        }
    });

    document.querySelectorAll('.l-v1-menu-item').forEach(item => {
        const link = item.querySelector('a');
        const hasSubmenu = item.querySelector('.l-v1-submenu');
    
        if (link && !hasSubmenu && !link.hasAttribute('data-action')) {
            item.addEventListener('click', function (e) {
                if (!e.target.closest('.l-v1-arrow-icon')) {
                    window.location.href = link.href;
                }
            });
        }
    });

    document.querySelectorAll('[data-action="logout"]').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('logout-form').submit();
        });
    });

    document.querySelectorAll('.l-v1-menu-subitem').forEach(item => {
        const link = item.querySelector('a');
        if (link) {
            item.addEventListener('click', function (e) {
                if (e.target.tagName.toLowerCase() !== 'a') {
                    window.location.href = link.href;
                }
            });
        }
    });
});


document.querySelectorAll('.l-v1-menu-item').forEach(item => {
    const l_v1_submenu = item.querySelector('.l-v1-submenu');

    if (l_v1_submenu) {
        item.addEventListener('click', () => {
            item.classList.toggle('open');
        });
    }
    
});

function clearHover() {
    document.querySelectorAll('.l-v1-menu-item, .l-v1-menu-subitem').forEach(el => {
    el.classList.remove('hover');
    });
}

document.querySelectorAll('.l-v1-menu-item, .l-v1-menu-subitem').forEach(el => {
    el.addEventListener('mouseenter', () => {
        clearHover();
        el.classList.add('hover');
    });
    el.addEventListener('mouseleave', () => {
        el.classList.remove('hover');
    });
});

aaaleftBtn.addEventListener('click', () => {
    aaaleftSidebar.classList.toggle('open');
});

aaarightBtn.addEventListener('click', () => {
    aaarightSidebar.classList.toggle('open');
});

