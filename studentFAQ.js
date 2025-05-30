// JavaScript for FAQ toggle functionality
document.addEventListener('DOMContentLoaded', function () {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');

        question.addEventListener('click', () => {
            // Close all other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                }
            });

            // Toggle current item
            item.classList.toggle('active');
        });
    });
    // Search functionality
    const searchInput = document.querySelector('.search-input');

    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();

            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    const noResultMessage = document.createElement('div');
    noResultMessage.textContent = 'No matching results found';
    noResultMessage.style.textAlign = 'center';
    noResultMessage.style.marginTop = '20px';
    noResultMessage.style.color = '#052c34';
    noResultMessage.style.display = 'none';
    document.querySelector('.faq-section').appendChild(noResultMessage);

    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        let found = false;

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();

            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
                found = true;
            } else {
                item.style.display = 'none';
            }
        });

        noResultMessage.style.display = found ? 'none' : 'block';
    });

});