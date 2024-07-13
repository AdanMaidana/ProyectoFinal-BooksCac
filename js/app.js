document.addEventListener('DOMContentLoaded', function () {

  const form = document.getElementById('itemForm');
  const itemsTableBody = document.getElementById('itemsTableBody');

  form.addEventListener('submit', function (event) {
    event.preventDefault();

    const formData = new FormData(form);
    const itemId = formData.get('id');

    // Construir objeto con los datos del formulario
    const data = {
      id: formData.get('id'),
      titulo: formData.get('titulo'),
      autor: formData.get('autor'),
      generos: formData.get('generos'),
      sinopsis: formData.get('sinopsis')
    };

    if (itemId) {
      updateItem(data);
    } else {
      createItem(data);
    }
  });

  function createItem(data) {
    fetch('http://localhost/CAC24145/api/api.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
    })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(result => {
        console.log('Success:', result);
        // Aquí podrías mostrar un mensaje de éxito al usuario, por ejemplo
        loadItems();  // Supongo que esta función carga los items actualizados
        document.getElementById('itemForm').reset(); // Limpiar el formulario después de crear el item
      })
      .catch(error => {
        console.error('Error:', error);
        // Aquí podrías mostrar un mensaje de error al usuario
        alert('Error al ingresar el item');
      });
  }



  // Función para cargar los elementos desde la API
  function loadItems() {
    fetch('http://localhost/CAC24145/api/api.php')
      .then(response => response.json())
      .then(data => {
        itemsTableBody.innerHTML = '';
        if (data.libros) {
          data.libros.forEach(libro => {
            const row = document.createElement('tr');
            row.innerHTML = `
                    <td>${libro.id}</td>
                    <td>${libro.titulo}</td>
                    <td>${libro.autor}</td>
                    <td>${libro.generos}</td>
                    <td>${libro.sinopsis}</td>               
                    <td>
                        <button onclick="deleteItem(${libro.id})">Eliminar</button>
                    </td>
                    <td>
                        <button onclick="editItem(
                        ${libro.id}, 
                        '${libro.titulo}', 
                        '${libro.autor}', 
                        '${libro.generos}', 
                        '${libro.sinopsis}' 
                        )">Editar</button>
                    </td>
                `;
            itemsTableBody.appendChild(row);
          });
        }
        else {
          console.error('No se encontraron libros');
        }
      })
      .catch(error => console.error('Error:', error));
  }


  // Función para borrar un libro
  function deleteItem(id) {
    fetch(`http://localhost/CAC24145/api/api.php?id=${id}`,
      {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        loadItems();

      })
      ;
    loadItems();
  }



  function updateItem(data) {
    fetch(`http://localhost/CAC24145/api/api.php?id=${data.id}`,
      {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(result => {
        console.log('Success:', result);

        loadItems();  // Supongo que esta función carga los items actualizados
        form.reset();
      })
      .catch(error => {
        console.error('Error:', error);
        // Aquí podrías mostrar un mensaje de error al usuario
        alert('Error al actualizar el item');
      });
  }



  window.editItem = function (id, titulo, autor, generos, sinopsis) {
    document.getElementById('id').value = id;
    document.getElementById('titulo').value = titulo;
    document.getElementById('autor').value = autor;
    document.getElementById('generos').value = generos;
    document.getElementById('sinopsis').value = sinopsis;
  };

  window.deleteItem = deleteItem;
  loadItems();
});