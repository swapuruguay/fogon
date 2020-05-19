const form = document.getElementById("form1");
const campos = document.getElementById("respo");
form.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const tabla = document.getElementById("respo");
  console.log(tabla.childNodes);

  if (tabla.childNodes.length > 2) {
    for (let i = 2; i < tabla.childNodes.length; i++) {
      let p = {
        nombre: tabla.childNodes[i].childNodes[0].childNodes[0].data,
        apellido: tabla.childNodes[i].childNodes[1].childNodes[0].data,
        parentezco: tabla.childNodes[i].childNodes[2].childNodes[0].data,
        documento: tabla.childNodes[i].childNodes[3].childNodes[0].data,
        sex: tabla.childNodes[i].childNodes[4].childNodes[0].data,
        fecha: tabla.childNodes[i].childNodes[5].childNodes[0].data,
      };
      const parent = document.createElement("input");
      parent.setAttribute("type", "hidden");
      parent.setAttribute("name", "parent[]");
      parent.setAttribute("value", JSON.stringify(p));
      form.appendChild(parent);
    }
  }

  // for (let h = 0; h < parientes.length; h++) {
  //   formData.append(`parientes[${h}].nombre`, parientes[h].nombre);
  //   formData.append(`parientes[${h}].parentezco`, parientes[h].parentezco);
  //   formData.append(`parientes[${h}].telefono`, parientes[h].telefono);
  //   formData.append(`parientes[${h}].celular`, parientes[h].celular);
  // }

  // recursiva();
  form.submit();
});

document.getElementById("add").addEventListener("click", add);
document.getElementById("remover").addEventListener("click", remove);

function recursiva() {
  let tabla = document.getElementById("respo");
  if (tabla.childNodes.length > 2) {
    remove();
    recursiva();
  }
}

function add() {
  let nombre = document.getElementById("name-parent");
  let apellido = document.getElementById("apellido-parent");
  let documento = document.getElementById("document");
  let sex = document.getElementById("sex");
  let parentezco = document.getElementById("parentezco");
  let fecha = document.getElementById("fecha");
  let tr = document.createElement("tr");
  let texto = `<td>${nombre.value}</td><td>${apellido.value}</td><td>${parentezco.value}</td><td>${documento.value}</td><td>${sex.value}</td><td>${fecha.value}</td>`;
  //eldiv.classList.add('col-sm-4')
  //let p = document.createElement('p')
  //  p.classList.add('form-control-static')
  //let text = document.createTextNode(nombre.value + ' ' +tel.value)
  //p.appendChild(text)
  tr.innerHTML = texto;
  campos.appendChild(tr);
  apellido.value = "";
  nombre.value = "";
  //sex.value = "";
  //parentezco.value = "";
  fecha.value = "";
}

function prueba() {
  let tabla = document.getElementById("respo");
  let fila = tabla.childNodes[2].childNodes[0].childNodes[0];
  console.log(fila);
}

function remove() {
  let tabla = document.getElementById("respo");
  if (tabla.childNodes.length > 2) {
    tabla.removeChild(tabla.lastChild);
  }
}
