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
        
        // Convert to lowercase
        let slug = title.toLowerCase();
        
        // Replace Cyrillic characters with Latin equivalents
        for (let cyrillic in cyrillicMap) {
            slug = slug.replace(new RegExp(cyrillic, 'g'), cyrillicMap[cyrillic]);
        }
        
        // Replace special characters and spaces with hyphens
        slug = slug.replace(/[^a-z0-9\s\-]/g, '');
        
        // Replace multiple spaces and hyphens with single hyphen
        slug = slug.replace(/[\s\-]+/g, '-');
        
        // Remove leading and trailing hyphens
        slug = slug.replace(/^-+|-+$/g, '');
        
        return slug;
    }

    // Auto-generate slug from title for all title inputs
    document.querySelectorAll('input[name*="[title]"], input[name*="title"], input[name="title"]').forEach(function(input) {
        input.addEventListener('input', function() {
            const title = this.value;
            const slug = generateSlug(title);
            
            // Find corresponding slug input
            const container = this.closest('.card-body, .form-group, .row, form') || document;
            const slugInput = container.querySelector('input[name*="[slug]"], input[name*="slug"], input[name="slug"]');
            
            if (slugInput && !slugInput.value) {
                slugInput.value = slug;
            }
        });
    });
});
</script>
@endpush 