const adminButton = document.querySelector('.adminLogin');
const tutorButton = document.querySelector('.signUp1');
const studentButton = document.querySelector('.signUp2');

adminButton.addEventListener('click', function() {
    window.location.href = ''; 
});

tutorButton.addEventListener('click', function() {
    window.location.href = 'animationSignIn.html'; 
});

studentButton.addEventListener('click', function() {
    window.location.href = 'studentSignUp.html'; 
});
   document.getElementById("signUpStudentBtn").addEventListener("click", function () {
    // يروح لصفحة animationSignIn.html
    window.location.href = "animationSignIn.html";
  });

  document.getElementById("signUpTutorBtn").addEventListener("click", function () {
    // يروح لصفحة animationSignIn.html
    window.location.href = "animationSignIn.html";
  });