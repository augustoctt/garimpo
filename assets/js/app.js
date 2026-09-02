document.querySelectorAll('form[data-validate]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
document.querySelectorAll('[data-confirm]').forEach((button) => {
    button.addEventListener('click', (event) => {
        if (!window.confirm(button.dataset.confirm)) event.preventDefault();
    });
});
if (window.lucide) window.lucide.createIcons();

const categoryIcons = {
    '100': 'shirt',
    '200': 'footprints',
    '300': 'briefcase-business',
    '400': 'gem',
    '500': 'lamp-floor',
    '600': 'book-open',
    '700': 'radio',
    '800': 'trophy',
    '900': 'toy-brick'
};
document.querySelectorAll('select[name="category"]').forEach((categorySelect) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'category-select-wrap';
    const icon = document.createElement('i');
    icon.id = 'categoryIcon';
    wrapper.appendChild(icon);
    categorySelect.parentNode.insertBefore(wrapper, categorySelect);
    wrapper.appendChild(categorySelect);
    const updateCategoryIcon = () => {
        const iconName = categoryIcons[categorySelect.value] || 'layers';
        const visibleIcon = wrapper.querySelector('svg, i');
        const replacement = document.createElement('i');
        replacement.id = 'categoryIcon';
        replacement.setAttribute('data-lucide', iconName);
        if (visibleIcon) visibleIcon.replaceWith(replacement);
        else wrapper.prepend(replacement);
    };
    categorySelect.addEventListener('change', updateCategoryIcon);
    updateCategoryIcon();
    if (window.lucide) window.lucide.createIcons();
});

document.querySelectorAll('.status-badge').forEach((badge) => {
    const row = badge.closest('tr');
    const editLink = row ? row.querySelector('a[href*="action=edit"]') : null;
    const match = editLink ? editLink.href.match(/[?&]id=(\d+)/) : null;
    const productId = match ? match[1] : null;
    if (!productId) return;
    let current = badge.textContent.trim();
    const wrapper = document.createElement('span');
    wrapper.className = 'status-control';
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = `status-badge status-editor ${badge.className.replace('status-badge', '').trim()}`;
    trigger.innerHTML = `<span>${current}</span><i data-lucide="chevron-down"></i>`;
    const menu = document.createElement('div');
    menu.className = 'status-menu';
    ['Disponível', 'Vendido', 'Reservado'].forEach((status) => {
        const option = document.createElement('button');
        option.type = 'button';
        option.className = `status-option ${status === current ? 'selected' : ''}`;
        option.dataset.status = status;
        option.innerHTML = `<span class="status-dot"></span>${status}`;
        option.addEventListener('click', async() => updateStatus(status));
        menu.append(option);
    });
    wrapper.append(trigger, menu);
    trigger.addEventListener('click', () => wrapper.classList.toggle('open'));
    const updateStatus = async(status) => {
        const previous = current;
        const response = await fetch('status_update.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ csrf: document.querySelector('meta[name="csrf-token"]').content, id: productId, status }) });
        if (!response.ok) {
            window.alert('Não foi possível atualizar o status.');
            return;
        }
        current = status;
        trigger.querySelector('span').textContent = status;
        trigger.className = `status-badge status-editor ${response.headers.get('X-Status-Class') || ''}`;
        wrapper.classList.remove('open');
    };
    wrapper.append(menu);
    badge.replaceWith(wrapper);
    if (window.lucide) window.lucide.createIcons();
});

document.addEventListener('click', (event) => {
    document.querySelectorAll('.status-control.open').forEach((control) => {
        if (!control.contains(event.target)) control.classList.remove('open');
    });
});