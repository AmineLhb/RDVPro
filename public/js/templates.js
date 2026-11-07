function templateManager(el) {
    const templates = JSON.parse(el.getAttribute('data-templates'));
    const previewUrl = el.getAttribute('data-preview-url');

    return {
        templates: templates,
        activeTemplate: null,
        activeTemplateId: null,
        renderedPreview: '',
        renderTimeout: null,
        
        init() {
            if (this.templates.length > 0) {
                this.selectTemplate(this.templates[0]);
            }
        },
        
        selectTemplate(tpl) {
            this.activeTemplate = tpl;
            this.activeTemplateId = tpl.id;
            this.debouncedRender();
        },
        
        debouncedRender() {
            if (this.renderTimeout) clearTimeout(this.renderTimeout);
            this.renderTimeout = setTimeout(() => {
                this.fetchPreview();
            }, 300);
        },
        
        fetchPreview() {
            if (!this.activeTemplate) return;
            
            fetch(previewUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    body: this.activeTemplate.body
                })
            })
            .then(response => response.json())
            .then(data => {
                this.renderedPreview = data.rendered;
            })
            .catch(err => {
                this.renderedPreview = this.activeTemplate.body;
            });
        }
    };
}
