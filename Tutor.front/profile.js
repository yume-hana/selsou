let isEditing = false;
    const fields = ['first_nameT', 'last_nameT', 'phone_numberT', 'Email_addressT', 'Address'];

    window.addEventListener('DOMContentLoaded', () => {
      fetch('http://localhost/LMW-PROJET/Tutor.back/profile.php')
        .then(response => response.json())
        .then(data => {
          fields.forEach(field => {
            document.getElementById(field + '_display').innerText = data[field] || '';
            document.getElementById(field).value = data[field] || '';
          });
        })
        // .catch(error => {
        //   console.error('Error fetching data:', error);
        //   alert('فشل في تحميل البيانات ❌');
        // });
    });

    function toggleEdit() {
      isEditing = !isEditing;
      fields.forEach(field => {
        const displayEl = document.getElementById(field + '_display');
        const inputEl = document.getElementById(field);

        if (isEditing) {
          displayEl.style.display = 'none';
          inputEl.style.display = 'block';
          document.getElementById('editBtn').style.display = 'none';
          document.getElementById('saveBtn').style.display = 'block';
        } else {
          displayEl.style.display = 'block';
          inputEl.style.display = 'none';
          document.getElementById('editBtn').style.display = 'block';
          document.getElementById('saveBtn').style.display = 'none';
        }
      });
    }

    function submitProfile() {
      const data = {};
      fields.forEach(field => {
        data[field] = document.getElementById(field).value;
      });

      fetch('http://localhost/LMW-PROJET/Tutor.back/profile.php', {
        method: 'PUT',
        credentials: "include",
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
        .then(response => {
          if (!response.ok) throw new Error(' Failed to update profile');
          return response.json();
        })
        .then(result => {
          fields.forEach(field => {
            document.getElementById(field + '_display').innerText = data[field];
            document.getElementById(field).value = data[field];
          });
          toggleEdit();
          alert('✅ Profile updated successfully!');
        })
        .catch(error => {
          console.error('Error updating profile:', error);
          alert('❌ Failed to update profile');
        });
    }