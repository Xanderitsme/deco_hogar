const formularios_ajax = document.querySelectorAll(".FormularioAjax");

formularios_ajax.forEach(formularios => {
  formularios.addEventListener("submit", function (e) {
    e.preventDefault();

    Swal.fire({
      title: "¿Estás seguro?",
      text: "¿Quieres realizar esta acción?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, realizar",
      cancelButtonText: "No, cancelar"
    }).then((result) => {
      if (result.isConfirmed) {

        let data = new FormData(this);
        let method = this.getAttribute("method");
        let action = this.getAttribute("action");

        let encabezados = new Headers();

        let config = {
          method: method,
          headers: encabezados,
          mode: 'cors',
          caches: 'no-cache',
          body: data
        };

        fetch(action, config)
          .then(respuesta => respuesta.json())
          .then(respuesta => {
            return alertas_ajax(respuesta);
          });
      }
    });
  });
});

function alertas_ajax(alerta) {
  if (alerta.tipo == "simple") {
    Swal.fire({
      icon: alerta.icono,
      title: alerta.titulo,
      text: alerta.texto,
      confirmButtonText: 'Aceptar'
    });

  } else if (alerta.tipo == "recargar") {
    Swal.fire({
      icon: alerta.icono,
      title: alerta.titulo,
      text: alerta.texto,
      confirmButtonText: 'Aceptar'
    }).then((result) => {
      if (result.isConfirmed) {
        location.reload();
      }
    });

  } else if (alerta.tipo == "limpiar") {
    Swal.fire({
      icon: alerta.icono,
      title: alerta.titulo,
      text: alerta.texto,
      confirmButtonText: 'Aceptar'
    }).then((result) => {
      if (result.isConfirmed) {
        document.querySelector(".FormularioAjax").reset();
      }
    });

  } else if (alerta.tipo == "redireccionar") {
    window.location.href = alerta.url;
  }
}

let btn_exit = document.getElementById("btn_exit");

btn_exit.addEventListener("click", function (e) {
  e.preventDefault();

  Swal.fire({
    title: "¿Quieres salir del sistema?",
    text: "La sesión actual se cerrará y saldrás del sistema",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, salir",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {

      let url = this.getAttribute("href");
      window.location.href = url;
    }
  });
});

const formularios_sin_confirmacion = document.querySelectorAll(".FormularioSinConfirmacion");

formularios_sin_confirmacion.forEach(formularios => {
  formularios.addEventListener("submit", function (e) {
    e.preventDefault();

    let data = new FormData(this);
    let method = this.getAttribute("method");
    let action = this.getAttribute("action");

    let encabezados = new Headers();

    let config = {
      method: method,
      headers: encabezados,
      mode: 'cors',
      caches: 'no-cache',
      body: data
    };

    fetch(action, config)
      .then(respuesta => respuesta.json())
      .then(respuesta => {
        return alertas_ajax(respuesta);
      });
  });
});