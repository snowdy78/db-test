function generateCaptcha(n) {
    // Очищаем контейнер, если он уже был заполнен
    const captchaContainer = document.querySelector('.captcha-image');
    if (!captchaContainer) return;
    captchaContainer.innerHTML = '';

    // Размер холста
    const width = captchaContainer.clientWidth;
    const height = captchaContainer.clientHeight;

    // Создаем SVG-элемент
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    svg.setAttribute('width', width);
    svg.setAttribute('height', height);

    // Рисуем n + 1 кругов
    for (let i = 0; i < n + 1; i++) {
        // Случайный диаметр круга
        const diameter = Math.floor(Math.random() * 91) + 10;
        const radius = diameter / 2;

        // Случайные координаты центра круга
        const xCenter = Math.floor(Math.random() * (width - diameter)) + radius;
        const yCenter = Math.floor(Math.random() * (height - diameter)) + radius;

        // Случайный цвет круга
        const red = Math.floor(Math.random() * 256);
        const green = Math.floor(Math.random() * 256);
        const blue = Math.floor(Math.random() * 256);
        const color = `rgba(${red}, ${green}, ${blue}, 0.25)`; // Прозрачность 0.5

        // Создаем круг
        const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', xCenter);
        circle.setAttribute('cy', yCenter);
        circle.setAttribute('r', radius);
        circle.setAttribute('fill', color);
        circle.setAttribute('stroke-width', 0); // Без контура

        // Добавляем круг в SVG
        svg.appendChild(circle);
    }
    captchaContainer.appendChild(svg);
}
function validateCaptcha(event, n) {
    const captchaInput = document.querySelector('.captcha-input');
    if (captchaInput.value != n + 1) {
        alert('Incorrect captcha');
        event.stopImmediatePropagation();
        event.preventDefault();
    }
}
