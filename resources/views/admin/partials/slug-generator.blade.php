@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cyrillic to Latin character mapping
    const cyrillicMap = {
        // Cyrillic letters
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo',
        'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm',
        'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
        'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'sch',
        'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya',
        // Azerbaijani specific characters
        'ə': 'e', 'ğ': 'gh', 'ü': 'u', 'ş': 'sh', 'ı': 'i', 'ö': 'o', 'ç': 'ch',
        // Uppercase Cyrillic letters
        'А': 'a', 'Б': 'b', 'В': 'v', 'Г': 'g', 'Д': 'd', 'Е': 'e', 'Ё': 'yo',
        'Ж': 'zh', 'З': 'z', 'И': 'i', 'Й': 'y', 'К': 'k', 'Л': 'l', 'М': 'm',
        'Н': 'n', 'О': 'o', 'П': 'p', 'Р': 'r', 'С': 's', 'Т': 't', 'У': 'u',
        'Ф': 'f', 'Х': 'h', 'Ц': 'ts', 'Ч': 'ch', 'Ш': 'sh', 'Щ': 'sch',
        'Ъ': '', 'Ы': 'y', 'Ь': '', 'Э': 'e', 'Ю': 'yu', 'Я': 'ya',
        // Uppercase Azerbaijani specific characters
        'Ə': 'e', 'Ğ': 'gh', 'Ü': 'u', 'Ş': 'sh', 'I': 'i', 'Ö': 'o', 'Ç': 'ch',
    };

    // Function to generate slug from title with Cyrillic support
    function generateSlug(title) {
        if (!title) return '';
        let slug = title.toLowerCase();
        for (let cyrillic in cyrillicMap) {
            slug = slug.replace(new RegExp(cyrillic, 'g'), cyrillicMap[cyrillic]);
        }
        slug = slug.replace(/[^a-z0-9\s\-]/g, '');
        slug = slug.replace(/[\s\-]+/g, '-');
        slug = slug.replace(/^-+|-+$/g, '');
        return slug;
    }

    // For each title input, update the corresponding slug input for the same language index
    // Only on create page, not on edit page
    if (!document.querySelector('.edit-slug-btn')) {
        // For translates (used in categories, etc.)
        document.querySelectorAll('input[name^="translates"][name$="[title]"]').forEach(function(titleInput) {
            titleInput.addEventListener('input', function() {
                const match = this.name.match(/translates\[(\d+)\]\[title\]/);
                if (!match) return;
                const idx = match[1];
                const slugInput = document.querySelector('input[name="translates[' + idx + '][slug]"]');
                if (slugInput && (!slugInput.dataset.touched || slugInput.value === '' || slugInput.value === generateSlug(this.defaultValue))) {
                    slugInput.value = generateSlug(this.value);
                }
            });
        });
        
        // For translations (used in products)
        document.querySelectorAll('input[name^="translations"][name$="[title]"]').forEach(function(titleInput) {
            titleInput.addEventListener('input', function() {
                const match = this.name.match(/translations\[(\d+)\]\[title\]/);
                if (!match) return;
                const languageId = match[1];
                const slugInput = document.querySelector('input[name="translations[' + languageId + '][slug]"]');
                if (slugInput && (!slugInput.dataset.touched || slugInput.value === '' || slugInput.value === generateSlug(this.defaultValue))) {
                    slugInput.value = generateSlug(this.value);
                }
            });
        });
        
        // If user manually edits slug, mark it as touched so it won't auto-update anymore
        document.querySelectorAll('input[name^="translates"][name$="[slug]"], input[name^="translations"][name$="[slug]"]').forEach(function(slugInput) {
            slugInput.addEventListener('input', function() {
                this.dataset.touched = 'true';
            });
        });
    }
});
</script>
@endpush 