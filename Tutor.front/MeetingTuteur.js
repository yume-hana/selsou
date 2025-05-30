// document.addEventListener("DOMContentLoaded", async () => {
//   const allBtn = document.querySelector(".AllMeeting_active");
//   const completedBtn = document.querySelector(".Completed");
//   const notCompletedBtn = document.querySelector(".Not_Completed");
//   const meetingList = document.querySelector(".ListMeeting");

//   let meetings = await fetchMeetingsFromDB();

//   const completedStates = ["Completed", "Accepted", "Rescheduled"];
//   const notCompletedStates = ["Missed", "Canceled", "Rejected", "Pending"];

//   function renderMeetings(filter) {
//     meetingList.innerHTML = "";

//     const filtered = meetings.filter(m => {
//       if (filter === "completed") return completedStates.includes(m.state);
//       if (filter === "not_completed") return notCompletedStates.includes(m.state);
//       return true; // all
//     });

//     if (filtered.length === 0) {
//       meetingList.innerHTML = "<p>No meetings found.</p>";
//       return;
//     }

//     filtered.forEach((m, index) => {
//       const div = document.createElement("div");
//       div.className = "MeetingBox";
//       div.id = `meeting-${index}`;

//       div.innerHTML = `
//         <p data-key="state" id="stateText-${index}">${m.state}</p>
//         <p data-key="date" id="dateText-${index}">${m.date}</p>
//         <p data-key="time" id="timeText-${index}">${m.time}</p>
//         <p data-key="location" id="locationText-${index}">${m.location}</p>
//         <p data-key="content" id="contentText-${index}">${m.content}</p>

//         <div class="default-actions" style="display:flex; gap: 10px; margin-top: 10px;">
//           <button id="editBtn-${index}">Edit</button>
//           <button id="deleteBtn-${index}">Delete</button>
//         </div>
//         <div class="edit-actions" style="display:none; gap: 10px; margin-top: 10px;">
//           <button id="saveBtn-${index}">Save</button>
//           <button id="cancelBtn-${index}">Cancel</button>
//         </div>
//       `;
//       meetingList.appendChild(div);

//       attachMeetingEvents(div, m, index);
//     });
//   }

//   function attachMeetingEvents(form, meetingData, index) {
//     const editBtn = form.querySelector(`#editBtn-${index}`);
//     const deleteBtn = form.querySelector(`#deleteBtn-${index}`);
//     const saveBtn = form.querySelector(`#saveBtn-${index}`);
//     const cancelBtn = form.querySelector(`#cancelBtn-${index}`);

//     const keys = ["state", "date", "time", "location", "content"];

//     editBtn.addEventListener('click', () => toggleEdit(true, index, meetingData));
    
//     saveBtn.addEventListener('click', async () => {
//       const updatedData = {};
//       keys.forEach(key => {
//         const inputElement = document.getElementById(`${key}Input-${index}`);
//         updatedData[key] = inputElement.value;
//       });
//       await updateMeetingInDB(meetingData.id, updatedData);
//       Object.assign(meetingData, updatedData);
//       toggleEdit(false, index, meetingData);
//     });

//     cancelBtn.addEventListener('click', () => toggleEdit(false, index, meetingData));

//     deleteBtn.addEventListener('click', async () => {
//       if(confirm("Are you sure you want to delete this meeting?")) {
//         await deleteMeetingFromDB(meetingData.id);
//         form.remove();
//         meetings = meetings.filter(m => m.id !== meetingData.id);
//       }
//     });
//   }

//   function toggleEdit(isEditing, index, data) {
//     const keys = ["state", "date", "time", "location", "content"];
//     const form = document.getElementById(`meeting-${index}`);

//     const stateOptions = [
//       { value: "Pending", text: "Pending" },
//       { value: "Accepted", text: "Accepted" },
//       { value: "Rescheduled", text: "Rescheduled" },
//       { value: "Rejected", text: "Rejected" },
//       { value: "Canceled", text: "Canceled" },
//       { value: "Completed", text: "Completed" },
//       { value: "Missed", text: "Missed" }
//     ];

//     keys.forEach(key => {
//       const oldEl = document.getElementById(`${key}Text-${index}`) || document.getElementById(`${key}Input-${index}`);
//       if (!oldEl) return;

//       if (isEditing) {
//         let inputElement;
//         if (key === "state") {
//           inputElement = document.createElement('select');
//           inputElement.id = `${key}Input-${index}`;
//           stateOptions.forEach(option => {
//             const optionElement = document.createElement('option');
//             optionElement.value = option.value;
//             optionElement.textContent = option.text;
//             if (option.value === data[key]) optionElement.selected = true;
//             inputElement.appendChild(optionElement);
//           });
//         } else {
//           inputElement = document.createElement('input');
//           inputElement.id = `${key}Input-${index}`;
//           inputElement.value = data[key];
//           if (key === "date") inputElement.type = "date";
//           else if (key === "time") inputElement.type = "time";
//           else inputElement.type = "text";
//         }
//         oldEl.replaceWith(inputElement);
//       } else {
//         const span = document.createElement('p');
//         span.id = `${key}Text-${index}`;
//         span.dataset.key = key;
//         span.textContent = data[key];
//         oldEl.replaceWith(span);
//       }
//     });

//     form.querySelector('.default-actions').style.display = isEditing ? 'none' : 'flex';
//     form.querySelector('.edit-actions').style.display = isEditing ? 'flex' : 'none';
//   }

//   renderMeetings("all"); // العرض الأولي
//   setActive(allBtn);     // هذي تفعيل زر All تلقائيًا عند تحميل الصفحة

//   allBtn.addEventListener("click", () => {
//     renderMeetings("all");
//     setActive(allBtn);
//   });

//   completedBtn.addEventListener("click", () => {
//     renderMeetings("completed");
//     setActive(completedBtn);
//   });

//   notCompletedBtn.addEventListener("click", () => {
//     renderMeetings("not_completed");
//     setActive(notCompletedBtn);
//   });

//   // دالة تغير حالة الزر النشط فقط
//   function setActive(activeBtn) {
//     [allBtn, completedBtn, notCompletedBtn].forEach(btn => {
//       btn.classList.remove("active");
//     });
//     activeBtn.classList.add("active");
//   }
// });

// // API Functions
// async function fetchMeetingsFromDB() {
//   const res = await fetch('/api/meetings');
//   if (!res.ok) return [];
//   return res.json();
// }

// async function updateMeetingInDB(id, data) {
//   await fetch(`/api/meetings/${id}`, {
//     method: 'PUT',
//     headers: { 'Content-Type': 'application/json' },
//     body: JSON.stringify(data)
//   });
// }

// async function deleteMeetingFromDB(id) {
//   await fetch(`/api/meetings/${id}`, { method: 'DELETE' });
// }




document.addEventListener("DOMContentLoaded", async () => {
  const allBtn = document.querySelector(".AllMeeting_active");
  const completedBtn = document.querySelector(".Completed");
  const notCompletedBtn = document.querySelector(".Not_Completed");
  const meetingList = document.querySelector(".ListMeeting");

  let meetings = await fetchMeetingsFromDB();

  const completedStates = ["completed", "accepted", "rescheduled"];
  const notCompletedStates = ["missed", "canceled", "rejected", "pending"];

  function renderMeetings(filter) {
    meetingList.innerHTML = "";

    const filtered = meetings.filter(m => {
      if (filter === "completed") return completedStates.includes(m.state_MT);
      if (filter === "not_completed") return notCompletedStates.includes(m.state_MT);
      return true; // all
    });

    if (filtered.length === 0) {
      meetingList.innerHTML = "<p>No meetings found.</p>";
      return;
    }

    filtered.forEach((m, index) => {
      const div = document.createElement("div");
      div.className = "MeetingBox";
      div.id = `meeting-${index}`;

      div.innerHTML = `
        <p data-key="state_MT" id="state_MTText-${index}">${m.state_MT}</p>
        <p data-key="Meeting_date" id="Meeting_dateText-${index}">${m.Meeting_date}</p>
        <p data-key="Meeting_time" id="Meeting_timeText-${index}">${m.Meeting_time}</p>
        <p data-key="Meeting_location" id="Meeting_locationText-${index}">${m.Meeting_location}</p>
        <p data-key="content_MT" id="content_MTText-${index}">${m.content_MT}</p>

        <div class="default-actions" style="display:flex; gap: 10px; margin-top: 10px;">
          <button id="editBtn-${index}">Edit</button>
          <button id="deleteBtn-${index}">Delete</button>
        </div>
        <div class="edit-actions" style="display:none; gap: 10px; margin-top: 10px;">
          <button id="saveBtn-${index}">Save</button>
          <button id="cancelBtn-${index}">Cancel</button>
        </div>
      `;
      meetingList.appendChild(div);

      attachMeetingEvents(div, m, index);
    });
  }

  function attachMeetingEvents(form, meetingData, index) {
    const editBtn = form.querySelector(`#editBtn-${index}`);
    const deleteBtn = form.querySelector(`#deleteBtn-${index}`);
    const saveBtn = form.querySelector(`#saveBtn-${index}`);
    const cancelBtn = form.querySelector(`#cancelBtn-${index}`);

    const keys = ["state_MT", "Meeting_date", "Meeting_time", "Meeting_location", "content_MT"];

    editBtn.addEventListener('click', () => toggleEdit(true, index, meetingData));
    
    saveBtn.addEventListener('click', async () => {
      const updatedData = {};
      keys.forEach(key => {
        const inputElement = document.getElementById(`${key}Input-${index}`);
        updatedData[key] = inputElement.value;
      });
      await updateMeetingInDB(meetingData.registration_nbr, meetingData.Tutor_ID, meetingData.Meeting_date, updatedData);
      Object.assign(meetingData, updatedData);
      toggleEdit(false, index, meetingData);
    });

    cancelBtn.addEventListener('click', () => toggleEdit(false, index, meetingData));

    deleteBtn.addEventListener('click', async () => {
      if(confirm("Are you sure you want to delete this meeting?")) {
        await deleteMeetingFromDB(meetingData.registration_nbr, meetingData.Tutor_ID, meetingData.Meeting_date);
        form.remove();
        meetings = meetings.filter(m =>
          m.registration_nbr !== meetingData.registration_nbr ||
          m.Tutor_ID !== meetingData.Tutor_ID ||
          m.Meeting_date !== meetingData.Meeting_date
        );
      }
    });
  }

  function toggleEdit(isEditing, index, data) {
    const keys = ["state_MT", "Meeting_date", "Meeting_time", "Meeting_location", "content_MT"];
    const form = document.getElementById(`meeting-${index}`);

    const stateOptions = [
      "pending", "accepted", "rejected", "rescheduled", "completed", "missed", "canceled"
    ];

    keys.forEach(key => {
      const oldEl = document.getElementById(`${key}Text-${index}`) || document.getElementById(`${key}Input-${index}`);
      if (!oldEl) return;

      if (isEditing) {
        let inputElement;
        if (key === "state_MT") {
          inputElement = document.createElement('select');
          inputElement.id = `${key}Input-${index}`;
          stateOptions.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option;
            optionElement.textContent = option;
            if (option === data[key]) optionElement.selected = true;
            inputElement.appendChild(optionElement);
          });
        } else {
          inputElement = document.createElement('input');
          inputElement.id = `${key}Input-${index}`;
          inputElement.value = data[key];
          if (key === "Meeting_date") inputElement.type = "date";
          else if (key === "Meeting_time") inputElement.type = "time";
          else inputElement.type = "text";
        }
        oldEl.replaceWith(inputElement);
      } else {
        const span = document.createElement('p');
        span.id = `${key}Text-${index}`;
        span.dataset.key = key;
        span.textContent = data[key];
        oldEl.replaceWith(span);
      }
    });

    form.querySelector('.default-actions').style.display = isEditing ? 'none' : 'flex';
    form.querySelector('.edit-actions').style.display = isEditing ? 'flex' : 'none';
  }

  renderMeetings("all"); // العرض الأولي
  setActive(allBtn);     // تفعيل زر All تلقائيًا عند تحميل الصفحة

  allBtn.addEventListener("click", () => {
    renderMeetings("all");
    setActive(allBtn);
  });

  completedBtn.addEventListener("click", () => {
    renderMeetings("completed");
    setActive(completedBtn);
  });

  notCompletedBtn.addEventListener("click", () => {
    renderMeetings("not_completed");
    setActive(notCompletedBtn);
  });

  // دالة تغير حالة الزر النشط فقط
  function setActive(activeBtn) {
    [allBtn, completedBtn, notCompletedBtn].forEach(btn => {
      btn.classList.remove("active");
    });
    activeBtn.classList.add("active");
  }
});

// API Functions
async function fetchMeetingsFromDB() {
  const res = await fetch('/api/meetings');
  if (!res.ok) return [];
  return res.json();
}

async function updateMeetingInDB(registration_nbr, Tutor_ID, Meeting_date, data) {
  await fetch(`/api/meetings/${registration_nbr}/${Tutor_ID}/${Meeting_date}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  });
}

async function deleteMeetingFromDB(registration_nbr, Tutor_ID, Meeting_date) {
  await fetch(`/api/meetings/${registration_nbr}/${Tutor_ID}/${Meeting_date}`, { method: 'DELETE' });
}
