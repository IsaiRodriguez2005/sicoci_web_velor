

$(function () {
  /* initialize the external events
    -----------------------------------------------------------------*/
  function ini_events(ele) {
    ele.each(function () {
      // create an Event Object (https://fullcalendar.io/docs/event-object)
      // it doesn't need to have a start or end
      var eventObject = {
        title: $.trim($(this).text()), // use the elemFent's text as the event title
      };

      // store the Event Object in the DOM element so we can get to it later
      $(this).data("eventObject", eventObject);

      // make the event draggable using jQuery UI
      /*
        $(this).draggable({
          zIndex        : 1070,F
          revert        : true, // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        })
        */
    });
  }

  ini_events($("#external-events div.external-event"));

  /* initialize the calendar
    -----------------------------------------------------------------*/
  //Date for the calendar events (dummy data)
  var date = new Date();
  var d = date.getDate(),
    m = date.getMonth(),
    y = date.getFullYear();

  var Calendar = FullCalendar.Calendar;
  //var Draggable = FullCalendar.Draggable;

  var containerEl = document.getElementById("external-events");
  var checkbox = document.getElementById("drop-remove");
  var calendarEl = document.getElementById("calendar");

  get_citas_agenda().then(function (resultado) {
    // Convierte el resultado a un objeto JavaScript (array de objetos)
    let citas = JSON.parse(resultado);


    
    let eventos = citas.map(cita => {
      if(Number( $("#id_terapeuta").val() ) == 0){
        return {
          title: cita.nombre_personal,
          start: cita.fecha_agenda,
          backgroundColor: '#00a65a',
          borderColor: '#00a65a',
          allDay: false
        };
      } else{
        return {
          title: cita.nombre_cliente,
          start: cita.fecha_agenda,
          backgroundColor: '#00a65a',
          borderColor: '#00a65a',
          allDay: false
        };
      }
    });


    var calendarEl = document.getElementById("calendar");

    if(Number( $("#id_terapeuta").val() ) == 0){
      var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "dayGridMonth,timeGridWeek,timeGridDay",
        },
        locale: 'es', // idioma español
        themeSystem: "bootstrap",
        initialView: 'timeGridDay',
        //defaulView:'day',
        buttonText: { // Textos de botones
          today: 'Hoy',
          month: 'Mes',
          week: 'Semana',
          day: 'Día',
          list: 'list'
        },
        events: eventos, // eventos de promesa
        editable: false,
        droppable: true,
        drop: function (info) {
          if (checkbox.checked) {
            info.draggedEl.parentNode.removeChild(info.draggedEl);
          }
        }
      });
    } else {
      var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "dayGridMonth,timeGridWeek,timeGridDay",
        },
        locale: 'es', // idioma español
        themeSystem: "bootstrap",
        initialView: 'timeGridWeek',
        //defaulView:'day',
        buttonText: { // Textos de botones
          today: 'Hoy',
          month: 'Mes',
          week: 'Semana',
          day: 'Día',
          list: 'list'
        },
        events: eventos, // eventos de promesa
        editable: false,
        droppable: true,
        drop: function (info) {
          if (checkbox.checked) {
            info.draggedEl.parentNode.removeChild(info.draggedEl);
          }
        }
      });
    }

    // renderizar el calendario
    calendar.render();
  }).catch(function (error) {
    console.error("Error al cargar las citas:", error);
  });
  // $('#calendar').fullCalendar()

  /* ADDING EVENTS */
  var currColor = "#3c8dbc"; //Red by default
  // Color chooser button
  $("#color-chooser > li > a").click(function (e) {
    e.preventDefault();
    // Save color
    currColor = $(this).css("color");
    // Add color effect to button
    $("#add-new-event").css({
      "background-color": currColor,
      "border-color": currColor,
    });
  });
  $("#add-new-event").click(function (e) {
    e.preventDefault();
    // Get value and make sure it is not null
    var val = $("#new-event").val();
    if (val.length == 0) {
      return;
    }

    // Create events
    var event = $("<div />");
    event
      .css({
        "background-color": currColor,
        "border-color": currColor,
        color: "#fff",
      })
      .addClass("external-event");
    event.text(val);
    $("#external-events").prepend(event);

    // Add draggable funtionality
    ini_events(event);

    // Remove event from text input
    $("#new-event").val("");
  });
});



function get_citas_agenda() {
  id_terapeuta = $("#id_terapeuta").val()

  //console.log(id_terapeuta)
  if(Number(id_terapeuta) == 0){
    return new Promise(function (resolve, reject) {
      $.ajax({
        cache: false,
        url: 'componentes/citas/cargar_historial_calendario.php',
        type: 'POST',
        dataType: 'html',
        data: {},
      }).done(function (resultado) {
        //console.log('hola')
        resolve(resultado);
      }).fail(function (jqXHR, textStatus, errorThrown) {
        reject(errorThrown);
      });
    });
  } else {
    return new Promise(function (resolve, reject) {
      $.ajax({
        cache: false,
        url: 'componentes/citas/cargar_historial_calendario.php',
        type: 'POST',
        dataType: 'html',
        data: {'id_terapeuta' : id_terapeuta},
      }).done(function (resultado) {
        //console.log(resultado)
        resolve(resultado);
      }).fail(function (jqXHR, textStatus, errorThrown) {
        reject(errorThrown);
      });
    });
  }

}