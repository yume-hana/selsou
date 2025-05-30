// Elements
const questionListContainer = document.getElementById("QuestionListContainer");
const allButton = document.querySelector(".AllQuestion");
const answeredButton = document.querySelector(".Answered");
const unansweredButton = document.querySelector(".Unanswered");

let questions = [];
let currentFilter = "all";

// Fetching questions from the database
async function fetchQuestions() {
  try {
    const response = await fetch('/api/questions'); // Expected to return questions with their answers
    if (!response.ok) throw new Error('Network error');
    questions = await response.json();
    displayQuestions(currentFilter);
  } catch (err) {
    console.error('Error loading questions:', err);
  }
}

function displayQuestions(filter = "all") {
  questionListContainer.innerHTML = "";
  currentFilter = filter;

  allButton.classList.toggle("active", filter === "all");
  answeredButton.classList.toggle("active", filter === "answered");
  unansweredButton.classList.toggle("active", filter === "unanswered");

  let filteredQuestions = questions;
  if (filter === "answered") {
    filteredQuestions = questions.filter(q => q.answer && q.answer.trim() !== "");
  } else if (filter === "unanswered") {
    filteredQuestions = questions.filter(q => !q.answer || q.answer.trim() === "");
  }

  filteredQuestions.forEach((q, index) => {
    const hasAnswer = q.answer && q.answer.trim() !== "";
    const questionBox = document.createElement("div");
    questionBox.classList.add("QuestionBox");

    const questionHeader = document.createElement("div");
    questionHeader.classList.add("question-header");

    // Display question text with date and time
    const questionText = document.createElement("span");
    questionText.textContent = `Q: ${q.contentQ}`;

    const questionDateTime = document.createElement("small");
    questionDateTime.style.marginLeft = "10px";
    questionDateTime.style.color = "#666";
    questionDateTime.textContent = `(${q.quest_date || "No date"} ${q.quest_time || ""})`;

    let actionElement;
    if (hasAnswer) {
      actionElement = document.createElement("span");
      actionElement.classList.add("toggle-arrow");
      actionElement.innerHTML = "&#9654;";
      actionElement.title = "Display the answer";
    } else {
      actionElement = document.createElement("button");
      actionElement.textContent = "Answer";
      actionElement.classList.add("answer-btn");
    }

    questionHeader.appendChild(questionText);
    questionHeader.appendChild(questionDateTime);
    questionHeader.appendChild(actionElement);

    const hiddenContent = document.createElement("div");
    hiddenContent.classList.add("hidden-content");
    hiddenContent.style.display = "none";

    if (hasAnswer) {
      const answerText = document.createElement("div");
      answerText.classList.add("answer-text");
      answerText.textContent = `A: ${q.answer}`;

      const answerDateTime = document.createElement("small");
      answerDateTime.style.marginLeft = "10px";
      answerDateTime.style.color = "#666";
      answerDateTime.textContent = `(${q.ans_date || "No date"} ${q.ans_time || ""})`;

      hiddenContent.appendChild(answerText);
      hiddenContent.appendChild(answerDateTime);
    } else {
      const answerInputContainer = document.createElement("div");
      answerInputContainer.classList.add("input-container");

      const answerInput = document.createElement("textarea");
      answerInput.classList.add("answer-input");
      answerInput.placeholder = "Enter your answer...";
      answerInput.style.display = "block";

      const saveBtn = document.createElement("button");
      saveBtn.textContent = "Save";
      saveBtn.classList.add("save-btn");
      saveBtn.style.display = "block";

      answerInputContainer.appendChild(answerInput);
      answerInputContainer.appendChild(saveBtn);
      hiddenContent.appendChild(answerInputContainer);

      saveBtn.addEventListener("click", async () => {
        const newAnswer = answerInput.value.trim();
        if (newAnswer !== "") {
          try {
            await fetch('/api/answer', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                quest_nbr: q.quest_nbr, // Important to send the question ID to link answer
                answer: newAnswer
              })
            });
            fetchQuestions();
            alert("✅ Answer saved successfully!");
          } catch (error) {
            console.error('Error saving answer:', error);
            alert("❌ Failed to save the answer!");
          }
        } else {
          alert("❌ Please enter your answer!");
        }
      });
    }

    if (hasAnswer) {
      actionElement.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleAnswer(hiddenContent, actionElement);
      });
      questionHeader.addEventListener("click", () => {
        toggleAnswer(hiddenContent, actionElement);
      });
    } else {
      actionElement.addEventListener("click", (e) => {
        e.stopPropagation();
        openQuestion(hiddenContent);
      });
    }

    questionBox.appendChild(questionHeader);
    questionBox.appendChild(hiddenContent);
    questionListContainer.appendChild(questionBox);
  });

  if (filteredQuestions.length === 0) {
    const emptyMessage = document.createElement("div");
    emptyMessage.classList.add("empty-questions");
    emptyMessage.textContent = filter === "answered"
      ? "No answered questions"
      : filter === "unanswered"
        ? "No unanswered questions"
        : "No questions available";
    questionListContainer.appendChild(emptyMessage);
  }
}

function openQuestion(contentElement) {
  document.querySelectorAll(".hidden-content").forEach(div => div.style.display = "none");
  contentElement.style.display = "block";
}

function toggleAnswer(contentElement, arrowElement) {
  const isVisible = contentElement.style.display === "block";
  document.querySelectorAll(".hidden-content").forEach(div => div.style.display = "none");
  document.querySelectorAll(".toggle-arrow").forEach(arrow => arrow.innerHTML = "&#9654;");

  if (!isVisible) {
    contentElement.style.display = "block";
    arrowElement.innerHTML = "&#9660;";
  }
}

allButton.addEventListener("click", () => displayQuestions("all"));
answeredButton.addEventListener("click", () => displayQuestions("answered"));
unansweredButton.addEventListener("click", () => displayQuestions("unanswered"));

// Fetch questions when the page loads
fetchQuestions();


// // Elements
// const questionListContainer = document.getElementById("QuestionListContainer");
// const allButton = document.querySelector(".AllQuestion");
// const answeredButton = document.querySelector(".Answered");
// const unansweredButton = document.querySelector(".Unanswered");

// let questions = [];
// let currentFilter = "all";

// // Fetching questions from the database
// async function fetchQuestions() {
//   try {
//     const response = await fetch('/api/questions');
//     if (!response.ok) throw new Error('Network error');
//     questions = await response.json();
//     displayQuestions(currentFilter);
//   } catch (err) {
//     console.error('Error loading questions:', err);
//   }
// }

// function displayQuestions(filter = "all") {
//   questionListContainer.innerHTML = "";
//   currentFilter = filter;

//   allButton.classList.toggle("active", filter === "all");
//   answeredButton.classList.toggle("active", filter === "answered");
//   unansweredButton.classList.toggle("active", filter === "unanswered");

//   let filteredQuestions = questions;
//   if (filter === "answered") {
//     filteredQuestions = questions.filter(q => q.answer && q.answer.trim() !== "");
//   } else if (filter === "unanswered") {
//     filteredQuestions = questions.filter(q => !q.answer || q.answer.trim() === "");
//   }

//   filteredQuestions.forEach((q, index) => {
//     const hasAnswer = q.answer && q.answer.trim() !== "";
//     const questionBox = document.createElement("div");
//     questionBox.classList.add("QuestionBox");

//     const questionHeader = document.createElement("div");
//     questionHeader.classList.add("question-header");

//     const questionText = document.createElement("span");
//     questionText.textContent = "Q: " + q.question;

//     let actionElement;
//     if (hasAnswer) {
//       actionElement = document.createElement("span");
//       actionElement.classList.add("toggle-arrow");
//       actionElement.innerHTML = "&#9654;";
//       actionElement.title = " Display the answer";
//     } else {
//       actionElement = document.createElement("button");
//       actionElement.textContent = "Answer";
//       actionElement.classList.add("answer-btn");
//     }

//     questionHeader.appendChild(questionText);
//     questionHeader.appendChild(actionElement);

//     const hiddenContent = document.createElement("div");
//     hiddenContent.classList.add("hidden-content");
//     hiddenContent.style.display = "none";

//     if (hasAnswer) {
//       const answerText = document.createElement("div");
//       answerText.classList.add("answer-text");
//       answerText.textContent = "A: " + q.answer;
//       hiddenContent.appendChild(answerText);
//     } else {
//       const answerInputContainer = document.createElement("div");
//       answerInputContainer.classList.add("input-container");

//       const answerInput = document.createElement("textarea");
//       answerInput.classList.add("answer-input");
//       answerInput.placeholder = "Enter your answer...";
//       answerInput.style.display = "block";

//       const saveBtn = document.createElement("button");
//       saveBtn.textContent = "Save";
//       saveBtn.classList.add("save-btn");
//       saveBtn.style.display = "block";

//       answerInputContainer.appendChild(answerInput);
//       answerInputContainer.appendChild(saveBtn);
//       hiddenContent.appendChild(answerInputContainer);

//       saveBtn.addEventListener("click", async () => {
//         const newAnswer = answerInput.value.trim();
//         if (newAnswer !== "") {
//           try {
//             await fetch('/api/answer', {
//               method: 'POST',
//               headers: { 'Content-Type': 'application/json' },
//               body: JSON.stringify({
//                 question: q.question,
//                 answer: newAnswer
//               })
//             });
//             fetchQuestions();
//             alert("✅ Answer saved successfully!");
//           } catch (error) {
//             console.error('Error saving answer:', error);
//             alert("❌ Failed to save the answer!");
//           }
//         } else {
//           alert("❌ Please enter your answer!");
//         }
//       });
//     }

//     if (hasAnswer) {
//       actionElement.addEventListener("click", (e) => {
//         e.stopPropagation();
//         toggleAnswer(hiddenContent, actionElement);
//       });
//       questionHeader.addEventListener("click", () => {
//         toggleAnswer(hiddenContent, actionElement);
//       });
//     } else {
//       actionElement.addEventListener("click", (e) => {
//         e.stopPropagation();
//         openQuestion(hiddenContent);
//       });
//     }

//     questionBox.appendChild(questionHeader);
//     questionBox.appendChild(hiddenContent);
//     questionListContainer.appendChild(questionBox);
//   });

//   if (filteredQuestions.length === 0) {
//     const emptyMessage = document.createElement("div");
//     emptyMessage.classList.add("empty-questions");
//     emptyMessage.textContent = filter === "answered"
//       ? "No answered questions"
//       : filter === "unanswered"
//         ? "No unanswered questions"
//         : "No questions available";
//     questionListContainer.appendChild(emptyMessage);
//   }
// }

// function openQuestion(contentElement) {
//   document.querySelectorAll(".hidden-content").forEach(div => div.style.display = "none");
//   contentElement.style.display = "block";
// }

// function toggleAnswer(contentElement, arrowElement) {
//   const isVisible = contentElement.style.display === "block";
//   document.querySelectorAll(".hidden-content").forEach(div => div.style.display = "none");
//   document.querySelectorAll(".toggle-arrow").forEach(arrow => arrow.innerHTML = "&#9654;");

//   if (!isVisible) {
//     contentElement.style.display = "block";
//     arrowElement.innerHTML = "&#9660;";
//   }
// }

// allButton.addEventListener("click", () => displayQuestions("all"));
// answeredButton.addEventListener("click", () => displayQuestions("answered"));
// unansweredButton.addEventListener("click", () => displayQuestions("unanswered"));

// // Fetch questions when the page loads
// fetchQuestions();









