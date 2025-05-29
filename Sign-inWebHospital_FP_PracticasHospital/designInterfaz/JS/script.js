"use strict";

// Lista de países por continentes.
const paises ={
  "África": ["Argelia", "Angola", "Benín", "Botsuana", "Burkina Faso", "Burundi", "Cabo Verde", "Camerún", "Chad", "Comoras", "Congo", "República Democrática del Congo", "Costa de Marfil", "Egipto", "Eritrea", "Esuatini", "Etiopía", "Gabón", "Gambia", "Ghana", "Guinea", "Guinea-Bisáu", "Guinea Ecuatorial", "Kenia", "Lesoto", "Liberia", "Libia", "Madagascar", "Malaui", "Malí", "Marruecos", "Mauricio", "Mauritania", "Mozambique", "Namibia", "Níger", "Nigeria", "República Centroafricana", "Ruanda", "Santo Tomé y Príncipe", "Senegal", "Seychelles", "Sierra Leona", "Somalia", "Sudáfrica", "Sudán", "Sudán del Sur", "Tanzania", "Togo", "Túnez", "Uganda", "Yibuti", "Zambia", "Zimbabue"],

  "América": ["Antigua y Barbuda", "Argentina", "Bahamas", "Barbados", "Belice", "Bolivia", "Brasil", "Canadá", "Chile", "Colombia", "Costa Rica", "Cuba", "Dominica", "Ecuador", "El Salvador", "Estados Unidos", "Granada", "Guatemala", "Guyana", "Haití", "Honduras", "Jamaica", "México", "Nicaragua", "Panamá", "Paraguay", "Perú", "República Dominicana", "San Cristóbal y Nieves", "San Vicente y las Granadinas", "Santa Lucía", "Surinam", "Trinidad y Tobago", "Uruguay", "Venezuela"],

  "Antártida": ["Antártida"],

  "Asia": ["Afganistán", "Arabia Saudita", "Armenia", "Azerbaiyán", "Bangladés", "Baréin", "Bután", "Camboya", "China", "Corea del Norte", "Corea del Sur", "Emiratos Árabes Unidos", "Filipinas", "Georgia", "India", "Indonesia", "Irak", "Irán", "Israel", "Japón", "Jordania", "Kazajistán", "Kirguistán", "Kuwait", "Laos", "Líbano", "Malasia", "Maldivas", "Mongolia", "Myanmar", "Nepal", "Omán", "Pakistán", "Palestina", "Qatar", "Rusia", "Singapur", "Siria", "Sri Lanka", "Tailandia", "Taiwán", "Tayikistán", "Timor Oriental", "Turkmenistán", "Turquía", "Uzbekistán", "Vietnam", "Yemen"],

  "Europa": ["Albania", "Alemania", "Andorra", "Austria", "Bélgica", "Bielorrusia", "Bosnia y Herzegovina", "Bulgaria", "Chequia", "Chipre", "Croacia", "Dinamarca", "Eslovaquia", "Eslovenia", "España", "Estonia", "Finlandia", "Francia", "Grecia", "Hungría", "Irlanda", "Islandia", "Italia", "Kosovo", "Letonia", "Liechtenstein", "Lituania", "Luxemburgo", "Macedonia del Norte", "Malta", "Moldavia", "Mónaco", "Montenegro", "Noruega", "Países Bajos", "Polonia", "Portugal", "Reino Unido", "Rumanía", "San Marino", "Serbia", "Suecia", "Suiza", "Ucrania", "Vaticano"],

  "Oceanía": ["Australia", "Fiyi", "Islas Marshall", "Islas Salomón", "Micronesia", "Nauru", "Nueva Zelanda", "Palau", "Papúa Nueva Guinea", "Samoa", "Tonga", "Tuvalu", "Vanuatu"]
};

// Declara una constante  con la que obtener el elemento "<select>" " del HTML con class="paisLista".
const selects = document.querySelectorAll(".paisLista");

// Recorre cada uno de esos elementos "<select>".
selects.forEach(select => {
    // Declara una constante con el que recorrer cada continente del objeto paises
    for (const continente in paises){
        // Crea una constante con un elemento "<optgroup>"" para agrupar los países por continentes.
        const grupo = document.createElement("optgroup");
        
        // Asignamos el nombre del continente como etiqueta del grupo.
        grupo.label = continente;

        // Ordenamos alfabéticamente los países dentro del continente y los recorremos uno por uno.
        paises[continente].sort().forEach(paisLista =>{
            // Declara una constante con elemento "<option>" para cada país.
            const option = document.createElement("option");

            // Establecemos el valor del atributo "value" (el valor que se enviará al servidor).
            option.value = paisLista;

            // Establece el texto visible que el usuario verá en el desplegable.
            option.textContent = paisLista;

            // Añade la opción al grupo correspondiente.
            grupo.appendChild(option);
        });

        // Añade el grupo completo de países al "<select>".
        select.appendChild(grupo);
    }
});