/* IIFI
 *
 * para que la funcion se llame inmediatamente, ademas de que mantiene un escope global
 */

( function() {

    obtenerTareas();

    // Variable gobal
    let tareas = [];
    let filtradas = [];

    // Boton para mostar el modal de agregar tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', function () {
        mostrarFormulario();
    });

    // Filtros de Busqueda
    const  filtros = document.querySelectorAll('#filtros input[type="radio"]');

    filtros.forEach( filtro => {
        filtro.addEventListener('input', filtrarTareas);
    });

    //Funcion de Filtrar Tareas
    function filtrarTareas(e) {
        const filtro = e.target.value;

        if (filtro !== '') {
            // Filtrar tareas por estado
            filtradas = tareas.filter(tarea => tarea.estado === filtro);
        } else {
            // dejamos la varible filtradas vacio, porque las vamos a mostrar todas
            filtradas = [];
        }

        mostrarTareas();
    }

    async function obtenerTareas() {

        // Construir la poeticion
        try {
            const id = obtenerProyecto();
            const url = `${location.origin}/api/tareas?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

           /* const { tareas } = resultado;*/
            tareas = resultado.tareas;

            mostrarTareas();

        } catch (error) {
            swal.fire('No Hemos Podido Consultar las Tareas!', error, 'error');
        }

    }

    // Deshabilitar boton de pendiente cuando no existan tareas pendientes
    function totalPendinte() {
        const totalPendiente = tareas.filter( tarea => tarea.estado === "0" );
        const  pendienteRadio = document.querySelector('#pendientes');

        if (totalPendiente.length === 0) {
            pendienteRadio.disabled = true;
        } else {
            pendienteRadio.disabled = false;
        }

    }

    // Deshabilitar boton de cmpletadas cuando no existan tareas completadas
    function totalCompletadas() {
        const totalCompletadas = tareas.filter( tarea => tarea.estado === "1" );
        const  completadasRadio = document.querySelector('#completadas');

        if (totalCompletadas.length === 0) {
            completadasRadio.disabled = true;
        } else {
            completadasRadio.disabled = false;
        }
    }

    // Scripting
    function mostrarTareas() {

        // Evitar tareas duplicadas limpiando el html
        lmpiarTreas();

        // Calcular tareas pendinetes y completadas
        totalPendinte();
        totalCompletadas();

        // Para verificar si el usuario seleciono filtradas, y si tiene algo usar filtradas y si no usar tareas
        const  arrayTareas = filtradas.length ? filtradas : tareas;

        const contenedortareas = document.querySelector('#listado-tareas');

        if ( arrayTareas.length === 0 ) {
            const textoNotareas = document.createElement('LI');
            textoNotareas.textContent = 'No Hay Tareas';
            textoNotareas.classList.add('no-tareas');

            contenedortareas.appendChild(textoNotareas);

            return;
        }

        // estados
        const estados = {
            0: 'Pendiente',
            1: 'Completa'
        }

        arrayTareas.forEach( tarea => {

            // Contenedor
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.targetId = tarea.id;
            contenedorTarea.classList.add('tarea');

            // Nombre
            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre;
            // Para editar tarea, el argumento true va a decir qeu esto esta deditando esta tarea
            // le pasamos la tarea para que tenga toda la info en el fomrulario
            nombreTarea.ondblclick = function () {
                mostrarFormulario(true, {...tarea});
            }

            // Opciones
            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            // Botones
            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnEstadoTarea.textContent =  estados[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;
            btnEstadoTarea.ondblclick = function () {
                cambiarEstadoTarea({...tarea});
            }

            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.textContent =  'Eliminar';
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.ondblclick = function () {
                confirmarEliminarTarea({...tarea});
            }


            // Generar Estructura
            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            contenedortareas.appendChild(contenedorTarea);

        } )
    }

    function mostrarFormulario(editar = false, tarea = {}) {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = ` 
            <form class="formulario nueva-tarea">
                <legend>${ editar ? 'Editar' : 'Agrega una nueva tarea' }</legend>
                <div class="campo">
                    <label for="tarea">Tarea</label>
                    <input type="text" 
                            id="tarea" 
                            name="tarea" 
                            placeholder="${ editar ? 'Edita la Tarea' : 'Agregar Tarea al Proyecto Actual' }"
                            value="${ editar ? tarea.nombre : '' }" 
                            />
                </div>
                <div class="opciones">
                    <input type="submit" 
                            class="submit-nueva-tarea" 
                            value="${ editar ? 'Guardar Cambios' : 'Agregar Tarea' }"/>
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;

        setTimeout( () => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 0);

        // Delegacion, trata de identificar a que elemento le damos click y con eso realizar cierta accion
        modal.addEventListener('click', function (e) {
            e.preventDefault();

            if ( e.target.classList.contains('cerrar-modal') || e.target.classList.contains('modal') ) {
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout( () => {
                    modal.remove();
                }, 500);
            }

            if ( e.target.classList.contains('submit-nueva-tarea') ) {

                const nombreTarea = document.querySelector('#tarea').value.trim();

                if (nombreTarea === '') {
                    // Mostrar una alerta de error
                    mostrarAlerta('El Nombre de la Tarea es Obligatorio', 'error', document.querySelector('.formulario legend'));

                    return;
                }

                if (editar) {
                    tarea.nombre = nombreTarea;
                    actualizarTarea(tarea);
                } else {
                    agregarTarea(nombreTarea);
                }

               /*submitFormularioNuevaTarea();*/

            }
            
        });

        document.querySelector('.dashboard').appendChild(modal);
    }

    /*function submitFormularioNuevaTarea() {
        const tarea = document.querySelector('#tarea').value.trim();

        if (tarea === '') {
            // Mostrar una alerta de error
            mostrarAlerta('El Nombre de la Tarea es Obligatorio', 'error', document.querySelector('.formulario legend'));

            return;
        }

        agregarTarea(tarea);

    }*/

    // Muestra un mensaje en la interfaz
    function mostrarAlerta(mensaje, tipo, referencia) {
        // Previene la creacion de multiples alertas
        const  alertaPrevia = document.querySelector('.alerta');
        if(alertaPrevia) {
            alertaPrevia.remove();
        }

        const  alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;

        // Inserta la alerta antes del legend
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);

        // Eliminar la alerta desp[ues de 5 segundos
        setTimeout( () => {
            alerta.remove();
        }, 5000);
    }

    // Consultar el servidor para agregar una nueva tarea al proyecto actual
    async function agregarTarea(tarea) {

        // Construir la poeticion
        const datos = new  FormData();
        datos.append('nombre', tarea);
        datos.append('proyectoId', obtenerProyecto());

        try {
            const url = `${location.origin}/api/tarea`;
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'));

            if (resultado.tipo === 'exito') {
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    modal.remove();
                }, 1000);

                // Agrergar el objecto de tarea al global de tareas
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: "0",
                    proyectoId: resultado.proyectoId,
                }

                // Agregra al arreglo de tareas con la info, tomar un copia, y agregar la nueva
                tareas = [...tareas, tareaObj];

                // Mostrar tareas
                mostrarTareas();

            }

        } catch (error) {
            swal.fire('No Hemos Podido Crear la Tarea!', error, 'error');
        }
    }

    // Cambia el estao d euna tarea
    function cambiarEstadoTarea(tarea) {

        const nuevEstado = tarea.estado === "1" ? "0" : "1";
        tarea.estado = nuevEstado;

        actualizarTarea(tarea);
    }

    async function actualizarTarea(tarea) {

        const { estado, id, nombre, proyectoId } = tarea;

        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto());

        // comprar datos antes de enviar al servidor
        /*for (let valor of datos.values()) {
            console.log(valor)
        }*/

        try {

            const url = `${location.origin}/api/tarea/actualizar`

            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            if (resultado.respuesta.tipo == 'exito') {
                swal.fire(resultado.respuesta.mensaje, resultado.respuesta.mensaje, 'success');

                const modal = document.querySelector('.modal');
                if (modal) {
                    modal.remove();
                }

                // itera en el arreglo de tareas pero creando un nuevo arreglo, sin mutar
                tareas = tareas.map( tareaMemoria => {

                    if ( tareaMemoria.id === id ) {
                        tareaMemoria.estado = estado;
                        tareaMemoria.nombre = nombre;
                    }

                    return tareaMemoria;
                } );

                // Mostrar tareas
                mostrarTareas();
            }

        } catch (error) {
            swal.fire('No Hemos Podido Actualizar la Tarea!', error, 'error');
        }

    }

    // ELimanr tarea
    function confirmarEliminarTarea(tarea) {

        Swal.fire({
            title: "Eliminar Tarea?",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: "No",
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarTarea(tarea);
            }
        });

    }

    async function eliminarTarea(tarea) {

        const { estado, id, nombre, proyectoId } = tarea;

        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto());

        // comprar datos antes de enviar al servidor
        /*for (let valor of datos.values()) {
            console.log(valor)
        }*/

        try {

            const url = `${location.origin}/api/tarea/eliminar`

            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            if (resultado.resultado.tipo == 'exito') {

                swal.fire('Eliminado!', resultado.mensaje, 'success');

                // no muta el arreglo original, y nos sirve para sacra uno y dejar solo uno
                tareas = tareas.filter( tareaMemoria => tareaMemoria.id !== tarea.id);

                // Mostrar tareas
                mostrarTareas();
            }

        } catch (error) {
            swal.fire('No Hemos Podido Eliminar la Tarea!', error, 'error');
        }

    }

    function obtenerProyecto() {

        // Leer parametros de la url
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries())
        return proyecto.id;

    }

    function lmpiarTreas() {
        const listadotareas = document.querySelector('#listado-tareas');

        // la fomr a mas rapida pero como se usara tanto no es recomendable
        /*listadotareas.innerHTML = '';*/

        // Busca el elmento y evalua que misentras alla contenido, lso va a ir eliminando 1 a 1, y es mas rapido
        while(listadotareas.firstChild) {
            listadotareas.removeChild(listadotareas.firstChild);
        }
    }

} )();