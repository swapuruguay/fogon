/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(cargar);

function cargar() {
    $("#busqueda").keyup(busca);
    $("#busquedae").keyup(buscae);


}

function busca(){
    var texto = $("#busqueda").val();
    if(texto != '') {
    $("#tabla").html('<p><img src="/public/img/ajax.gif" />Buscando, espere...</p>');
    $.post('/socios/findSocios/','texto='+texto+'&active=activo',
            escribe,'json');

    }  else {
        $("#tabla").html("No se ha introducido texto");
    }

}

function buscae(){
    var cadena = $("#busquedae").val();
    if(cadena !== '') {
    $("#tabla").html('<p><img src="/public/img/ajax.gif" />Buscando, espere...</p>');
    $.post('/socios/findSocios/0','texto='+cadena+'&active=0',
            function(respuesta) {
                var texto = '<tr><th>Nro.</th><th>Nombre</th><th>Apellido</th><th>Activar</th><th>Editar</th></tr>';
                for(var i = 0; i < respuesta.length; i++){

                    texto+='<tr>';
                    texto+= '<td>'+respuesta[i].id_socio+'</td><td>'+utf8_decode(respuesta[i].nombre)+'</td><td>'+utf8_decode(respuesta[i].apellido)+'</td>';
                    texto+='<td><a href="/socios/activar/'+respuesta[i].id_socio+'"><img src="/views/layout/default/img/active.png"></a></td>';
                    texto+='<td><a href="/socios/editar/'+respuesta[i].id_socio+'"><img src="/views/layout/default/img/edit.png"></a></td>';
                    texto+= '</tr>';
                }

    $("#tabla").html(texto);
            },'json');

    }  else {
        $("#tabla").html("No se ha introducido texto");
    }

}

function escribe(respuesta) {
    var texto = '<tr><td>Nro.</td><td>Nombre</td><td>Apellido</td><td></td><td></td></tr>';
    for(var i = 0; i < respuesta.length; i++){
        texto+='<tr>';
        texto+= '<td>'+respuesta[i].id_socio+'</td><td>'+utf8_decode(respuesta[i].nombre)+'</td><td>'+utf8_decode(respuesta[i].apellido)+'</td>';
        texto+='<td><a href="/socios/editar/'+respuesta[i].id_socio+'"><img src="/views/layout/default/img/edit.png"></a></td>\n\
        <td><a href="/socios/confirmar/'+respuesta[i].id_socio+'"><img src="/views/layout/default/img/delete.png"></a></td>';
        texto+= '</tr>';
    }

    $("#tabla").html(texto);

}



function escribeMov(respuesta) {
  let btn = document.getElementById('ver')
  let perfil = btn.dataset.perfil
    var texto = '<tr><th class="text-center">Fecha</th><th class="text-center">Tipo</th><th class="text-center">Importe</th>'
    texto+= (perfil == 1) ? '<th class="text-center">Eliminar</th>': '';
    texto+= '</tr>';
    var saldo = 0;
    for(var i = 0; i < respuesta.length; i++){
        saldo +=  parseInt(respuesta[i].importe);
        var txt = 'Pago';
        if(parseInt(respuesta[i].importe) > 0) {
            txt = 'Cuota';
        }
        texto+='<tr>';
        texto+= '<td class="text-center">'+respuesta[i].fecha_computo+'</td><td class="text-center">'+txt+'</td><td class="text-right">'+respuesta[i].importe+'</td>';
        texto+= (perfil == 1) ? '<td class="text-center"><a id="'+respuesta[i].id_cuota+'" href="#" class="eliminar"><img src="/views/layout/default/img/delete.png"></a></td>' : '';
        texto+= '</tr>';
    }

    $("#movs").html(texto);
    $("#total").html("Saldo: "+saldo);

}

function utf8_decode (strData) { // eslint-disable-line camelcase
  //  discuss at: http://locutus.io/php/utf8_decode/
  // original by: Webtoolkit.info (http://www.webtoolkit.info/)
  //    input by: Aman Gupta
  //    input by: Brett Zamir (http://brett-zamir.me)
  // improved by: Kevin van Zonneveld (http://kvz.io)
  // improved by: Norman "zEh" Fuchs
  // bugfixed by: hitwork
  // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
  // bugfixed by: Kevin van Zonneveld (http://kvz.io)
  // bugfixed by: kirilloid
  // bugfixed by: w35l3y (http://www.wesley.eti.br)
  //   example 1: utf8_decode('Kevin van Zonneveld')
  //   returns 1: 'Kevin van Zonneveld'
  var tmpArr = []
  var i = 0
  var c1 = 0
  var seqlen = 0
  strData += ''
  while (i < strData.length) {
    c1 = strData.charCodeAt(i) & 0xFF
    seqlen = 0
    // http://en.wikipedia.org/wiki/UTF-8#Codepage_layout
    if (c1 <= 0xBF) {
      c1 = (c1 & 0x7F)
      seqlen = 1
    } else if (c1 <= 0xDF) {
      c1 = (c1 & 0x1F)
      seqlen = 2
    } else if (c1 <= 0xEF) {
      c1 = (c1 & 0x0F)
      seqlen = 3
    } else {
      c1 = (c1 & 0x07)
      seqlen = 4
    }
    for (var ai = 1; ai < seqlen; ++ai) {
      c1 = ((c1 << 0x06) | (strData.charCodeAt(ai + i) & 0x3F))
    }
    if (seqlen === 4) {
      c1 -= 0x10000
      tmpArr.push(String.fromCharCode(0xD800 | ((c1 >> 10) & 0x3FF)))
      tmpArr.push(String.fromCharCode(0xDC00 | (c1 & 0x3FF)))
    } else {
      tmpArr.push(String.fromCharCode(c1))
    }
    i += seqlen
  }
  return tmpArr.join('')
}

function utf8_encode (argString) { // eslint-disable-line camelcase
  //  discuss at: http://locutus.io/php/utf8_encode/
  // original by: Webtoolkit.info (http://www.webtoolkit.info/)
  // improved by: Kevin van Zonneveld (http://kvz.io)
  // improved by: sowberry
  // improved by: Jack
  // improved by: Yves Sucaet
  // improved by: kirilloid
  // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
  // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
  // bugfixed by: Ulrich
  // bugfixed by: Rafał Kukawski (http://blog.kukawski.pl)
  // bugfixed by: kirilloid
  //   example 1: utf8_encode('Kevin van Zonneveld')
  //   returns 1: 'Kevin van Zonneveld'
  if (argString === null || typeof argString === 'undefined') {
    return ''
  }
  // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
  var string = (argString + '')
  var utftext = ''
  var start
  var end
  var stringl = 0
  start = end = 0
  stringl = string.length
  for (var n = 0; n < stringl; n++) {
    var c1 = string.charCodeAt(n)
    var enc = null
    if (c1 < 128) {
      end++
    } else if (c1 > 127 && c1 < 2048) {
      enc = String.fromCharCode(
        (c1 >> 6) | 192, (c1 & 63) | 128
      )
    } else if ((c1 & 0xF800) !== 0xD800) {
      enc = String.fromCharCode(
        (c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      )
    } else {
      // surrogate pairs
      if ((c1 & 0xFC00) !== 0xD800) {
        throw new RangeError('Unmatched trail surrogate at ' + n)
      }
      var c2 = string.charCodeAt(++n)
      if ((c2 & 0xFC00) !== 0xDC00) {
        throw new RangeError('Unmatched lead surrogate at ' + (n - 1))
      }
      c1 = ((c1 & 0x3FF) << 10) + (c2 & 0x3FF) + 0x10000
      enc = String.fromCharCode(
        (c1 >> 18) | 240, ((c1 >> 12) & 63) | 128, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      )
    }
    if (enc !== null) {
      if (end > start) {
        utftext += string.slice(start, end)
      }
      utftext += enc
      start = end = n + 1
    }
  }
  if (end > start) {
    utftext += string.slice(start, stringl)
  }
  return utftext
}
