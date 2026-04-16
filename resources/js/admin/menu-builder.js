// resources/js/admin/menu-builder.js
document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('mbItemForm');

    if (!form) return;

    // 👇 кнопки "Add Item" должны иметь data-section-id
    document.querySelectorAll('[data-mb-add-item]').forEach(btn => {

        btn.addEventListener('click', () => {

            const sectionId = btn.dataset.sectionId;
            const restaurantId = btn.dataset.restaurantId;

            // 1. вставляем section_id
            document.getElementById('mbItemSectionId').value = sectionId;

            // 2. ставим правильный action
            form.action = `/admin/restaurants/${restaurantId}/sections/${sectionId}/items`;

        });
    });

});
