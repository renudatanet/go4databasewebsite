document.addEventListener('DOMContentLoaded', function () {
  if (window.lucide) {
    lucide.createIcons();
  }

  var faqItems = document.querySelectorAll('.faq-item');

  faqItems.forEach(function (item) {
    var questionButton = item.querySelector('.faq-question');
    var answerContainer = item.querySelector('.faq-answer');

    questionButton.addEventListener('click', function () {
      var isOpen = item.classList.contains('active');

      faqItems.forEach(function (otherItem) {
        if (otherItem !== item) {
          otherItem.classList.remove('active');
          otherItem.querySelector('.faq-answer').style.maxHeight = null;
        }
      });

      if (isOpen) {
        item.classList.remove('active');
        answerContainer.style.maxHeight = null;
      } else {
        item.classList.add('active');
        answerContainer.style.maxHeight = answerContainer.scrollHeight + 'px';
      }
    });
  });
});
