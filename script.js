document.addEventListener("DOMContentLoaded", function () {
    const userForm = document.getElementById("userForm");
    const userList = document.getElementById("userList");
    const userIdField = document.getElementById("userId");

    function fetchUsers() {
        fetch("api.php")
            .then(response => response.json())
            .then(users => {
                userList.innerHTML = "";
                users.forEach(user => {
                    const li = document.createElement("li");
                    li.innerHTML = `
                        ${user.name} (${user.email}) - ${user.role || 'user'}
                        <div>
                            <button onclick="editUser(${user.id}, '${user.name}', '${user.email}', '${user.role || 'user'}')" title="Modifier">✏️</button>
                            <button onclick="deleteUser(${user.id})" title="Supprimer">❌</button>
                        </div>
                    `;
                    userList.appendChild(li);
                });
            })
            .catch(error => console.error('Erreur:', error));
    }

    userForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const role = document.getElementById("role").value;
        const userId = userIdField.value;

        if (userId) {
            // Modification d'utilisateur
            fetch("api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    action: 'update',
                    id: userId,
                    name: name,
                    email: email,
                    role: role
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la modification');
                }
                return response.json();
            })
            .then(() => {
                fetchUsers();
                userForm.reset();
                userIdField.value = "";
                document.querySelector('button[type="submit"]').textContent = "Ajouter";
            })
            .catch(error => console.error('Erreur:', error));
        } else {
            // Ajout d'utilisateur
            fetch("api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    action: 'add',
                    name: name,
                    email: email,
                    role: role
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de l\'ajout');
                }
                return response.json();
            })
            .then(() => {
                fetchUsers();
                userForm.reset();
            })
            .catch(error => console.error('Erreur:', error));
        }
    });

    window.editUser = function (id, name, email, role) {
        document.getElementById("name").value = name;
        document.getElementById("email").value = email;
        document.getElementById("role").value = role || 'user';
        userIdField.value = id;
        document.querySelector('button[type="submit"]').textContent = "Modifier";
    };

    window.deleteUser = function (id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
            fetch("api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    action: 'delete',
                    id: id
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la suppression');
                }
                return response.json();
            })
            .then(() => fetchUsers())
            .catch(error => console.error('Erreur:', error));
        }
    };

    // Chargement initial des utilisateurs
    fetchUsers();
});
