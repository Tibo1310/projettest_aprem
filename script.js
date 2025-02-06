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
                            <select onchange="changeRole(${user.id}, this.value)" title="Changer le rôle">
                                <option value="user" ${(user.role === 'user' || !user.role) ? 'selected' : ''}>Utilisateur</option>
                                <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                            </select>
                        </div>
                    `;
                    userList.appendChild(li);
                });
            });
    }

    userForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
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
                    email: email
                })
            })
            .then(response => response.json())
            .then(() => {
                fetchUsers();
                userForm.reset();
                userIdField.value = "";
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
                    role: 'user' // Rôle par défaut
                })
            })
            .then(response => response.json())
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
        userIdField.value = id;
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
            .then(() => fetchUsers())
            .catch(error => console.error('Erreur:', error));
        }
    };

    window.changeRole = function (id, newRole) {
        fetch("api.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: 'update',
                id: id,
                role: newRole
            })
        })
        .then(response => response.json())
        .then(() => fetchUsers())
        .catch(error => console.error('Erreur:', error));
    };

    // Chargement initial des utilisateurs
    fetchUsers();
});
