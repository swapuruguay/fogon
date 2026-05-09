document.addEventListener('DOMContentLoaded', () => {
    const busquedaInput = document.querySelector('#busqueda');
    const tabla = document.querySelector('#tabla');

    if (busquedaInput && tabla) {
        busquedaInput.addEventListener('keyup', () => busca(busquedaInput.value, tabla, 'activo'));
    }

    const busquedaeInput = document.querySelector('#busquedae');
    if (busquedaeInput && tabla) {
        busquedaeInput.addEventListener('keyup', () => busca(busquedaeInput.value, tabla, ''));
    }
});

async function busca(texto, tablaEl, active) {
    if (texto === '') {
        tablaEl.innerHTML = 'No se ha introducido texto';
        return;
    }

    tablaEl.innerHTML = '<p><img src="/public/img/ajax.gif" />Buscando, espere...</p>';

    try {
        const resp = await fetch('/socios/findSocios', {
            method: 'POST',
            body: new URLSearchParams({ texto, active })
        }).then(r => r.json());

        if (active === 'activo') {
            escribirSocios(resp, tablaEl);
        } else {
            escribirSociosEliminados(resp, tablaEl);
        }
    } catch (e) {
        console.error(e);
        tablaEl.innerHTML = 'Error en la búsqueda';
    }
}

function escribirSocios(resp, tablaEl) {

    let html = '<tr><th>Nro.</th><th>Nombre</th><th>Apellido</th><th></th><th></th></tr>';
    resp.forEach(s => {
        html += `<tr>
            <td>${s.id_socio}</td>
            <td>${s.nombre}</td>
            <td>${s.apellido}</td>
            <td><a href="/socios/editar/${s.id_socio}"><img src="/views/layout/default/img/edit.png"></a></td>
            <td><a href="/socios/confirmar/${s.id_socio}"><img src="/views/layout/default/img/delete.png"></a></td>
        </tr>`;
    });
    if (resp.length === 1) {
        html = `<tr><td>${resp[0].nombre} ${resp[0].apellido}</td></tr>`
    }
    tablaEl.innerHTML = html;
}

function escribirSociosEliminados(resp, tablaEl) {
    let html = '<tr><th>Nro.</th><th>Nombre</th><th>Apellido</th><th>Activar</th><th>Editar</th></tr>';
    resp.forEach(s => {
        html += `<tr>
            <td>${s.id_socio}</td>
            <td>${s.nombre}</td>
            <td>${s.apellido}</td>
            <td><a href="/socios/activar/${s.id_socio}"><img src="/views/layout/default/img/active.png"></a></td>
            <td><a href="/socios/editar/${s.id_socio}"><img src="/views/layout/default/img/edit.png"></a></td>
        </tr>`;
    });
    tablaEl.innerHTML = html;
}


function utf8_encode(argString) {
    if (argString === null || typeof argString === 'undefined') return '';
    const string = argString + '';
    let utftext = '';
    let start, end, stringl = 0;
    start = end = 0;
    stringl = string.length;
    for (let n = 0; n < stringl; n++) {
        let c1 = string.charCodeAt(n);
        let enc = null;
        if (c1 < 128) { end++; }
        else if (c1 > 127 && c1 < 2048) {
            enc = String.fromCharCode((c1 >> 6) | 192, (c1 & 63) | 128);
        } else if ((c1 & 0xF800) !== 0xD800) {
            enc = String.fromCharCode((c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128);
        } else {
            if ((c1 & 0xFC00) !== 0xD800) throw new RangeError('Unmatched trail surrogate');
            const c2 = string.charCodeAt(++n);
            if ((c2 & 0xFC00) !== 0xDC00) throw new RangeError('Unmatched lead surrogate');
            c1 = ((c1 & 0x3FF) << 10) + (c2 & 0x3FF) + 0x10000;
            enc = String.fromCharCode((c1 >> 18) | 240, ((c1 >> 12) & 63) | 128, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128);
        }
        if (enc !== null) {
            if (end > start) utftext += string.slice(start, end);
            utftext += enc;
            start = end = n + 1;
        }
    }
    if (end > start) utftext += string.slice(start, stringl);
    return utftext;
}