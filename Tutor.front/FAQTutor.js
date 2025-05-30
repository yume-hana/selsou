// Get elements from the page
const form = document.querySelector(".BOXcreateFAQ");
const questionInput = document.getElementById("questionFAQ");
const answerInput = document.getElementById("answerFAQ");
const faqListContainer = document.getElementById("faqListContainer");
const cancelBtn = document.getElementById("btnCancelFAQ");
const showAllBtn = document.querySelector(".All");
const showMineBtn = document.querySelector(".CreatedbyMe");

let currentFilter = "all";
let approvedFAQs = []; // Fetched from the server

// Fetch FAQs from the server
async function loadFAQs() {
  try {
    const response = await fetch("https://yourserver.com/api/faqs");
    approvedFAQs = await response.json();
    displayFAQs();
  } catch (error) {
    console.error("❌ Error loading FAQs:", error);
  }
}

// Display the list of FAQs
function displayFAQs() {
  faqListContainer.innerHTML = "";

  let filteredFAQs = approvedFAQs;
  if (currentFilter === "mine") {
    filteredFAQs = approvedFAQs.filter(faq => faq.createdByMe);
  }

  filteredFAQs.forEach((faq, index) => {
    const faqBox = document.createElement("div");
    faqBox.classList.add("FAQbox");

    const questionDiv = document.createElement("div");
    questionDiv.classList.add("question-item");

    const questionText = document.createElement("span");
    questionText.textContent = "Q: " + faq.question_content;

    const arrow = document.createElement("span");
    arrow.classList.add("arrow");
    arrow.innerHTML = "&#9654;";

    questionDiv.appendChild(questionText);
    questionDiv.appendChild(arrow);

    const answerDiv = document.createElement("div");
    answerDiv.classList.add("answer-item");
    answerDiv.style.display = "none";

    const answerText = document.createElement("div");
    answerText.textContent = "A: " + faq.answer_question;

    const deleteBtn = document.createElement("button");
    deleteBtn.textContent = "Delete";
    deleteBtn.classList.add("deleteBtn");
    deleteBtn.style.display = "none";

    deleteBtn.addEventListener("click", async (e) => {
      e.stopPropagation();
      try {
        await fetch(`https://yourserver.com/api/faqs/${faq.FAQ_nbr}`, {
          method: "DELETE",
        });
        await loadFAQs();
      } catch (error) {
        console.error("❌ Error deleting FAQ:", error);
      }
    });

    answerDiv.appendChild(answerText);
    answerDiv.appendChild(deleteBtn);

    faqBox.appendChild(questionDiv);
    faqBox.appendChild(answerDiv);
    faqListContainer.appendChild(faqBox);

    questionDiv.addEventListener("click", () => {
      const isAnswerVisible = answerDiv.style.display === "block";

      document.querySelectorAll(".answer-item").forEach(div => div.style.display = "none");
      document.querySelectorAll(".deleteBtn").forEach(btn => btn.style.display = "none");
      document.querySelectorAll(".arrow").forEach(arrow => arrow.innerHTML = "&#9654;");

      if (!isAnswerVisible) {
        answerDiv.style.display = "block";
        deleteBtn.style.display = "block";
        arrow.innerHTML = "&#9660;";
      }
    });
  });

  faqListContainer.scrollTop = faqListContainer.scrollHeight;
}

// Add new FAQ
form.addEventListener("submit", async function (e) {
  e.preventDefault();

  const question = questionInput.value.trim();
  const answer = answerInput.value.trim();

  if (!question || !answer) {
    alert("❌ Please fill in both question and answer");
    return;
  }

  try {
    await fetch("https://yourserver.com/api/faqs", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ question_content: question, answer_question: answer, createdByMe: true })
    });

    questionInput.value = "";
    answerInput.value = "";

    await loadFAQs();
  } catch (error) {
    console.error("❌ Error adding FAQ:", error);
  }
});

// Cancel button clears the fields
cancelBtn.addEventListener("click", function () {
  questionInput.value = "";
  answerInput.value = "";
});

// Switch between All and Created By Me
function updateActiveButton() {
  if (currentFilter === "all") {
    showAllBtn.classList.add("active");
    showMineBtn.classList.remove("active");
  } else {
    showMineBtn.classList.add("active");
    showAllBtn.classList.remove("active");
  }
}

showAllBtn.addEventListener("click", () => {
  currentFilter = "all";
  updateActiveButton();
  displayFAQs();
});

showMineBtn.addEventListener("click", () => {
  currentFilter = "mine";
  updateActiveButton();
  displayFAQs();
});

// Load on page start
updateActiveButton();
loadFAQs();



// // Get elements from the page
// const form = document.querySelector(".BOXcreateFAQ");
// const questionInput = document.getElementById("questionFAQ");
// const answerInput = document.getElementById("answerFAQ");
// const faqListContainer = document.getElementById("faqListContainer");
// const cancelBtn = document.getElementById("btnCancelFAQ");
// const showAllBtn = document.querySelector(".All");
// const showMineBtn = document.querySelector(".CreatedbyMe");

// let currentFilter = "all";
// let approvedFAQs = []; // Fetched from the server

// // Fetch FAQs from the server
// async function loadFAQs() {
//   try {
//     const response = await fetch("https://yourserver.com/api/faqs");
//     approvedFAQs = await response.json();
//     displayFAQs();
//   } catch (error) {
//     console.error("❌ Error loading FAQs:", error);
//   }
// }

// // Display the list of FAQs
// function displayFAQs() {
//   faqListContainer.innerHTML = "";

//   let filteredFAQs = approvedFAQs;
//   if (currentFilter === "mine") {
//     filteredFAQs = approvedFAQs.filter(faq => faq.createdByMe);
//   }

//   filteredFAQs.forEach((faq, index) => {
//     const faqBox = document.createElement("div");
//     faqBox.classList.add("FAQbox");

//     const questionDiv = document.createElement("div");
//     questionDiv.classList.add("question-item");

//     const questionText = document.createElement("span");
//     questionText.textContent = "Q: " + faq.question;

//     const arrow = document.createElement("span");
//     arrow.classList.add("arrow");
//     arrow.innerHTML = "&#9654;";

//     questionDiv.appendChild(questionText);
//     questionDiv.appendChild(arrow);

//     const answerDiv = document.createElement("div");
//     answerDiv.classList.add("answer-item");
//     answerDiv.style.display = "none";

//     const answerText = document.createElement("div");
//     answerText.textContent = "A: " + faq.answer;

//     const deleteBtn = document.createElement("button");
//     deleteBtn.textContent = "Delate";
//     deleteBtn.classList.add("delateBtn");
//     deleteBtn.style.display = "none";

//     deleteBtn.addEventListener("click", async (e) => {
//       e.stopPropagation();
//       try {
//         await fetch(`https://yourserver.com/api/faqs/${faq.id}`, {
//           method: "DELETE",
//         });
//         await loadFAQs();
//       } catch (error) {
//         console.error("❌ Error deleting FAQ:", error);
//       }
//     });

//     answerDiv.appendChild(answerText);
//     answerDiv.appendChild(deleteBtn);

//     faqBox.appendChild(questionDiv);
//     faqBox.appendChild(answerDiv);
//     faqListContainer.appendChild(faqBox);

//     questionDiv.addEventListener("click", () => {
//       const isAnswerVisible = answerDiv.style.display === "block";

//       document.querySelectorAll(".answer-item").forEach(div => div.style.display = "none");
//       document.querySelectorAll(".delateBtn").forEach(btn => btn.style.display = "none");
//       document.querySelectorAll(".arrow").forEach(arrow => arrow.innerHTML = "&#9654;");

//       if (!isAnswerVisible) {
//         answerDiv.style.display = "block";
//         deleteBtn.style.display = "block";
//         arrow.innerHTML = "&#9660;";
//       }
//     });
//   });

//   faqListContainer.scrollTop = faqListContainer.scrollHeight;
// }

// // Add new FAQ
// form.addEventListener("submit", async function (e) {
//   e.preventDefault();

//   const question = questionInput.value.trim();
//   const answer = answerInput.value.trim();

//   if (!question || !answer) {
//     alert("❌ Please fill in both question and answer");
//     return;
//   }

//   try {
//     await fetch("https://yourserver.com/api/faqs", {
//       method: "POST",
//       headers: { "Content-Type": "application/json" },
//       body: JSON.stringify({ question, answer, createdByMe: true })
//     });

//     questionInput.value = "";
//     answerInput.value = "";

//     await loadFAQs();
//   } catch (error) {
//     console.error("❌ Error adding FAQ:", error);
//   }
// });

// // Cancel button clears the fields
// cancelBtn.addEventListener("click", function () {
//   questionInput.value = "";
//   answerInput.value = "";
// });

// // Switch between All and Created By Me
// function updateActiveButton() {
//   if (currentFilter === "all") {
//     showAllBtn.classList.add("active");
//     showMineBtn.classList.remove("active");
//   } else {
//     showMineBtn.classList.add("active");
//     showAllBtn.classList.remove("active");
//   }
// }

// showAllBtn.addEventListener("click", () => {
//   currentFilter = "all";
//   updateActiveButton();
//   displayFAQs();
// });

// showMineBtn.addEventListener("click", () => {
//   currentFilter = "mine";
//   updateActiveButton();
//   displayFAQs();
// });

// // Load on page start
// updateActiveButton();
// loadFAQs();
