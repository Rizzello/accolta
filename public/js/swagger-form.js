const swaggerElement = document.querySelector('#swagger-ui');

if (swaggerElement && window.SwaggerUIBundle) {
    window.SwaggerUIBundle({
        url: swaggerElement.dataset.openapiUrl,
        dom_id: '#swagger-ui',
    });
}
