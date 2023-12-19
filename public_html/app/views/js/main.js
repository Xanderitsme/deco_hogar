document.addEventListener('DOMContentLoaded', () => {
  const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

  $navbarBurgers.forEach(el => {
    el.addEventListener('click', () => {

      const target = el.dataset.target;
      const $target = document.getElementById(target);

      el.classList.toggle('is-active');
      $target.classList.toggle('is-active');

    });
  });
});

function mostrarImagenPreview(event) {
  const input = event.target;
  const reader = new FileReader();

  reader.onload = function () {
    const imagenPrev = document.getElementById('imagen-usuario');
    imagenPrev.src = reader.result;
  };

  if (input.files && input.files[0]) {
    reader.readAsDataURL(input.files[0]);
  }
}

function mostrarImagenVistaPrevia(event) {
  const input = event.target;
  const fileCta = document.querySelector('.file-cta');

  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      fileCta.style.backgroundImage = `url('${e.target.result}')`;
    };

    reader.readAsDataURL(input.files[0]);
  }
}

function ocultarVistaPrevia() {
  const fileCta = document.querySelector('.file-cta');
  fileCta.style.backgroundImage = "none";
}
