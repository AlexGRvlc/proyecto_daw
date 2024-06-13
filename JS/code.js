$(document).ready(function () {
  // REGISTRO
  $("#btn_registro").on("click", (e) => {
    e.preventDefault();

    let form = $("#form_registro").serialize(),
      success = $("#registro_success"),
      fail = $("#registro_fail"),
      msg_fail = $("#msg_error");

    $.ajax({
      url: "../lib/validar_registro.php",
      data: form,
      type: "POST",
      dataType: "json",

      success: function (response) {
        if (response.error === true) {
          msg_fail.html(response.tipo_error);
          success.hide();
          fail.show();
        } else {
          fail.hide();
          $("#form_registro").hide();
          success.show();
          setTimeout(() => {
            $(location).attr("href", "login.php");
          }, 4000);
        }
      },
      error: function (jqXHR, status, error) {
        console.log(jqXHR.responseText);
        console.log(status);
        console.log(error);
      },
      complete: function () {
        console.log("envío completo");
      },
    });
  });

  // Log In
  $("#btn_login").on("click", function (e) {
    e.preventDefault();
    let datos_registro = $("#form-login").serialize();

    // Para trabajar dinámicamente con la aplicación
    $.ajax({
      type: "POST",
      url: "../lib/validar_login.php",
      data: datos_registro,
      dataType: "json",

      success: function (respuesta) {
        if (respuesta.error) {
          $("#oculto-login").css({ display: "block" });
          $(".alerta_wrapper").html(respuesta.tipo_error);
        } else {
          if (respuesta.rol === "administrador") {
            $(location).attr("href", "admin.php");
          } else {
            $(location).attr("href", "../pages/socios.php");
          }
        }
      },
      error: function (e) {
        console.log("Error en la solicitud AJAX:", e);
        console.log("Respuesta del servidor:", e.responseText);
      },
    });
  });

  // Log Out
  $("#logout").on("click", function () {
    console.log("logout");
    $.ajax({
      type: "POST",
      url: "../lib/logout.php",
      dataType: "html",
      success: function (e) {
        console.log("success");
        console.log(e);
        $(location).attr("href", "../index.html");
      },
      error: function (jqXHR, status, error) {
        console.log(jqXHR.responseText);
        console.log(status);
        console.log(error);
      },
    });
  });

  // EDITAR
  $("#btn_actualizar").on("click", function (e) {
    e.preventDefault();
    let form = $("#form_registro").serialize();

    $.ajax({
      type: "POST",
      url: "../lib/actualizar_socio.php",
      data: form,
      dataType: "json",
      success: function (response) {
        console.log(response.tipo_error);
        if (response.error === false) {
          $(".edit_success").show();
          setTimeout(() => {
            $(location).attr("href", "editar_socios.php");
          }, 2000);
        }
      },
    });
  });

  // ELIMINAR
  let id;
  $(".accion_eliminar").on("click", function (e) {
    e.preventDefault();
    id = $(this).data("id");
    console.log(id);

    $("#caja_eliminar").css({ display: "block" });
  });

  $("#si").on("click", function (e) {
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: "../lib/eliminar.php",
      data: { eliminar: id },
      dataType: "json",
      success: function (response) {
        if (response.estado === "ok") {
          console.log("ok desde code.js");
          console.log(response.msg);
          $("#caja_eliminar").css({ display: "none" });
          location.reload();
        } else {
          console.log("Error en la solicitud AJAX:", response.msg);
          alert("Ocurrió un error: " + response.msg);
        }
      },
      error: function (jqXHR, status, error) {
        console.log(jqXHR.responseText);
        console.log(status);
        console.log(error);
      },
    });
  });

  $("#no").on("click", function (e) {
    e.preventDefault();
    $("#caja_eliminar").css({ display: "none" });
  });

  //ACTIVIDADES
  $("#btn_semanal").on("click", (e) => {
    e.preventDefault();

    let form = $("#form_actividades").serialize(),
    msg_sucess = $(".success_msg"),
    msg_fail = $(".fail_msg");

    // Validación default
    let hasDefault = false;
    // para validar default
    let formArray = $("#form_actividades").serializeArray();
    // Recorre los datos del formulario y verifica si algún campo tiene el valor 'default'
    $.each(formArray, function (i, field) {
      if (field.value === "default") {
        hasDefault = true;
        return false; 
      }
    });

    if (hasDefault) {
      msg_fail.html("No pueden quedar horarios vacíos");
    } else {
      $.ajax({
        url: "../lib/validar_actividades.php",
        data: form,
        type: "POST",
        dataType: "json",

        success: function (response) {
          if (response.error === true) {
            console.log("error--> " + response.msg);
            msg_fail.html(response.tipo_error);
            // success.hide();
            // fail.show();
          } else {
            console.log(response.msg);
            msg_sucess.html(response.msg);
            setTimeout(() => {
              $(location).attr("href", "semanal.php");
            }, 3000);
          }
        },
        error: function (jqXHR, status, error) {
          console.log(jqXHR.responseText);
          console.log(status);
          console.log(error);
        },
        complete: function () {
          console.log("envío completo");
        },
      });
    }
  });
});
