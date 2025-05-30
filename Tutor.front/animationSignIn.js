document.addEventListener("DOMContentLoaded", function () {
    const isSignUpPage = document.querySelector(".infoTuteur") !== null;
    const isSignInPage = document.querySelector(".infoTuteurLOGIN") !== null;

    // Sign up
    if (isSignUpPage) {
        const form = document.querySelector(".infoTuteur");
        const signUpButton = document.querySelector(".SignUPToHome");

        signUpButton.addEventListener("click", function (event) {
            event.preventDefault();

            if (!validateSignUpForm()) return;

            const formData = {
                first_nameT: document.querySelector('input[name="first_name"]').value.trim(),
                last_nameT: document.querySelector('input[name="last_name"]').value.trim(),
                PasswordT: document.querySelector('input[name="password"]').value,
                phone_numberT: document.querySelector('input[name="phone"]').value.trim(),
                Email_addressT: document.querySelector('input[name="email"]').value.trim(),
                Address: document.querySelector('input[name="address"]').value.trim(),
                date_of_birthT: document.querySelector('input[name="dob"]').value,
                gender: document.querySelector('select[name="gender"]').value,
                quality: document.querySelector('select[name="quality"]').value
            };

            Swal.fire({
                title: 'Submitting Data...',
                text: 'Please wait',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("http://localhost/LMW-PROJET/Tutor.back/register.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) throw new Error(`Server error ${response.status}`);
                return response.json();
            })
            .then(data => handleSignUpResponse(data))
            .catch(error => {
                console.error("Error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while submitting your information. Please try again.',
                    confirmButtonText: 'OK'
                });
            });
        });
    }

    // Sign In
    if (isSignInPage) {
        const signInButton = document.querySelector(".SignUPToHome");

        signInButton.addEventListener("click", function (event) {
            event.preventDefault();

            const email = document.querySelector('input[name="email"]').value.trim();
            const password = document.querySelector('input[name="password"]').value;

            if (!validateSignInForm(email, password)) return;

            Swal.fire({
                title: 'Logging In...',
                text: 'Please wait',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            const data = {
                Email_addressT: email,
                PasswordT: password
            };

            fetch("http://localhost/LMW-PROJET/Tutor.back/login.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) throw new Error(`Server error ${response.status}`);
                return response.json();
            })
            .then(data => handleSignInResponse(data))
            .catch(error => {
                console.error("Error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while trying to log in. Please try again later.',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
});

// التحقق من صحة نموذج التسجيل
function validateSignUpForm() {
    const firstName = document.querySelector('input[name="first_name"]');
    if (!firstName.value.trim()) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Please enter your first name' });
        return false;
    }

    const lastName = document.querySelector('input[name="last_name"]');
    if (!lastName.value.trim()) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Please enter your last name' });
        return false;
    }

    const email = document.querySelector('input[name="email"]');
    if (!validateEmail(email.value)) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Please enter a valid email' });
        return false;
    }

    const password = document.querySelector('input[name="password"]');
    if (!validatePassword(password.value)) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Password must be at least 8 characters and contain numbers' });
        return false;
    }

    const phone = document.querySelector('input[name="phone"]');
    if (!validatePhone(phone.value)) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Please enter a valid phone number' });
        return false;
    }

    const address = document.querySelector('input[name="address"]');
    if (!address.value.trim()) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Please enter your address' });
        return false;
    }

    const dob = document.querySelector('input[name="dob"]');
    if (!dob.value) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Please enter your date of birth' });
        return false;
    }

    return true;
}

// التحقق من صحة نموذج تسجيل الدخول
function validateSignInForm(email, password) {
    if (!email || !validateEmail(email)) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Please enter a valid email address' });
        return false;
    }

    if (!password) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Please enter your password' });
        return false;
    }

    return true;
}

// التحقق من صحة البريد الإلكتروني
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// التحقق من صحة كلمة المرور
function validatePassword(password) {
    const regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
    return regex.test(password);
}

// التحقق من صحة رقم الهاتف
function validatePhone(phone) {
    const regex = /^\d{8,}$/;
    return regex.test(phone);
}

// التعامل مع الرد على التسجيل
function handleSignUpResponse(data) {
    if (data.status === "success") {
        Swal.fire({ icon: 'success', title: 'Registered!', text: 'Pending admin approval.' })
        .then(() => window.location.href = "registration_confirmation.html");
    } else {
        Swal.fire({ icon: 'error', title: 'Registration Error', text: data.message || 'Try again.' });
    }
}

// التعامل مع الرد على تسجيل الدخول
function handleSignInResponse(data) {
    if (data.status === "success") {
        Swal.fire({ icon: 'success', title: 'Login Successful!', text: 'Redirecting...' })
        .then(() => window.location.href = "TutorHome.html");
    } else if (data.status === "pending") {
        Swal.fire({ icon: 'info', title: 'Pending', text: 'Account under admin review.' });
    } else if (data.status === "rejected") {
        Swal.fire({ icon: 'error', title: 'Rejected', text: 'Contact administration.' });
    } else {
        Swal.fire({ icon: 'error', title: 'Login Error', text: data.message || 'Email or password incorrect.' });
    }
}










// document.addEventListener("DOMContentLoaded", function () {
//     // معرفة ما إذا كنا في صفحة التسجيل أو تسجيل الدخول
//     const isSignUpPage = document.querySelector(".infoTuteur") !== null;
//     const isSignInPage = document.querySelector(".infoTuteurLOGIN") !== null;  // تعديل السطر هنا

//     // التعامل مع نموذج التسجيل
//     if (isSignUpPage) {
//         const form = document.querySelector(".infoTuteur");
//         const signUpButton = document.querySelector(".SignUPToHome");

//         signUpButton.addEventListener("click", function (event) {
//             event.preventDefault(); // منع إرسال النموذج التلقائي

//             // التحقق من المدخلات
//             if (!validateSignUpForm()) {
//                 return; // إيقاف التنفيذ إذا كانت المدخلات غير صالحة
//             }

//             // جمع بيانات النموذج
//             const formData = {
//                 first_name: document.querySelector('input[name="first_name"]').value.trim(),
//                 last_name: document.querySelector('input[name="last_name"]').value.trim(),
//                 password: document.querySelector('input[name="password"]').value,
//                 phone: document.querySelector('input[name="phone"]').value.trim(),
//                 email: document.querySelector('input[name="email"]').value.trim(),
//                 address: document.querySelector('input[name="address"]').value.trim(),
//                 dob: document.querySelector('input[name="dob"]').value,
//                 gender: document.querySelector('select[name="gender"]').value,
//                 quality: document.querySelector('select[name="quality"]').value
//             };

//             // إظهار حالة التحميل
//             Swal.fire({
//                 title: 'Submitting Data...',
//                 text: 'Please wait',
//                 allowOutsideClick: false,
//                 showConfirmButton: false,
//                 willOpen: () => {
//                     Swal.showLoading();
//                 }
//             });

//             // إرسال البيانات إلى الخادم لتسجيل المستخدم وانتظار موافقة الإدارة
//             fetch("http://localhost/leadmyway/submit_tuteur.php", {
//                 method: "POST",
//                 headers: {
//                     "Content-Type": "application/json"
//                 },
//                 body: JSON.stringify(formData)
//             })
//             .then(response => {
//                 if (!response.ok) {
//                     throw new Error(`Server responded with status ${response.status}`);
//                 }
//                 return response.json();
//             })
//             .then(data => {
//                 handleSignUpResponse(data);
//             })
//             .catch(error => {
//                 console.error("Error:", error);
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Error',
//                     text: 'An error occurred while submitting your information. Please try again.',
//                     confirmButtonText: 'OK'
//                 });
//             });
//         });
//     }

//     // التعامل مع نموذج تسجيل الدخول
//     if (isSignInPage) {
//         const signInButton = document.querySelector(".SignUPToHome");

//         signInButton.addEventListener("click", function (event) {
//             event.preventDefault(); // منع إرسال النموذج التلقائي

//             const email = document.querySelector('input[name="email"]').value.trim();
//             const password = document.querySelector('input[name="password"]').value;

//             // التحقق من المدخلات
//             if (!validateSignInForm(email, password)) {
//                 return; // إيقاف التنفيذ إذا كانت المدخلات غير صالحة
//             }

//             // إظهار حالة التحميل
//             Swal.fire({
//                 title: 'Logging In...',
//                 text: 'Please wait',
//                 allowOutsideClick: false,
//                 showConfirmButton: false,
//                 willOpen: () => {
//                     Swal.showLoading();
//                 }
//             });

//             // إرسال البيانات إلى الخادم للتحقق منها
//             const data = {
//                 email: email,
//                 password: password
//             };

//             fetch("http://localhost/leadmyway/login_tuteur.php", { // تم تغيير العنوان إلى عنوان السيرفر الفعلي للتحقق من بيانات تسجيل الدخول
//                 method: "POST",
//                 headers: {
//                     "Content-Type": "application/json"
//                 },
//                 body: JSON.stringify(data)
//             })
//             .then(response => {
//                 if (!response.ok) {
//                     throw new Error(`Server responded with status ${response.status}`);
//                 }
//                 return response.json();
//             })
//             .then(data => {
//                 handleSignInResponse(data);
//             })
//             .catch(error => {
//                 console.error("Error:", error);
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Error',
//                     text: 'An error occurred while trying to log in. Please try again later.',
//                     confirmButtonText: 'OK'
//                 });
//             });
//         });
//     }
// });

// // وظيفة للتحقق من صحة مدخلات التسجيل
// function validateSignUpForm() {
//     // Validate first name
//     const firstNameInput = document.querySelector('input[name="first_name"]');
//     if (!firstNameInput.value.trim()) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: 'Please enter your first name',
//             confirmButtonText: 'OK'
//         });
//         return false;
//     }

//     // Validate last name
//     const lastNameInput = document.querySelector('input[name="last_name"]');
//     if (!lastNameInput.value.trim()) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: 'Please enter your last name',
//             confirmButtonText: 'OK'
//         });
//         return false;
//     }

//     // Validate email
//     const emailInput = document.querySelector('input[name="email"]');
//     if (!validateEmail(emailInput.value)) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: 'Please enter a valid email address',
//             confirmButtonText: 'OK'
//         });
//         return false;
//     }

//     // Validate password
//     const passwordInput = document.querySelector('input[name="password"]');
//     if (!validatePassword(passwordInput.value)) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: 'Password must be at least 8 characters long and include both letters and numbers',
//             confirmButtonText: 'OK'
//         });
//         return false;
//     }

//     // Validate phone number
//     const phoneInput = document.querySelector('input[name="phone"]');
//     if (!validatePhone(phoneInput.value)) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: 'Please enter a valid phone number',
//             confirmButtonText: 'OK'
//         });
//         return false;
//     }

//     // Validate address
//     const addressInput = document.querySelector('input[name="address"]');
//     if (!addressInput.value.trim()) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: 'Please enter your address',
//             confirmButtonText: 'OK'
//         });
//         return false;
//     }

//     // Validate date of birth
//     const dobInput = document.querySelector('input[name="dob"]');
//     if (!dobInput.value) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: 'Please enter your date of birth',
//             confirmButtonText: 'OK'
//         });
//         return false;
//     }

//     return true; // All inputs are valid
// }

// // وظيفة للتحقق من صحة مدخلات تسجيل الدخول
// function validateSignInForm(email, password) {
//     // Validate email field
//     if (!email) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: 'Please enter your email address',
//             confirmButtonText: 'OK'
//         });
//         return false;
//     }

//     // Validate email format
//     if (!validateEmail(email)) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: 'Please enter a valid email address',
//             confirmButtonText: 'OK'
//         });
//         return false;
//     }

//     // Validate password
//     if (!password) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Error',
//             text: 'Please enter your password',
//             confirmButtonText: 'OK'
//         });
//         return false;
//     }

//     return true; // All inputs are valid
// }

// // دوال المساعدة للتحقق من صحة المدخلات

// // التحقق من صحة البريد الإلكتروني
// function validateEmail(email) {
//     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//     return emailRegex.test(email);
// }

// // التحقق من صحة كلمة المرور
// function validatePassword(password) {
//     // على الأقل 8 أحرف وتحتوي على حرف واحد ورقم واحد على الأقل
//     const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
//     return passwordRegex.test(password);
// }

// // التحقق من صحة رقم الهاتف
// function validatePhone(phone) {
//     // تحقق من أن رقم الهاتف يحتوي على أرقام فقط ولا يقل عن 8 أرقام
//     const phoneRegex = /^\d{8,}$/;
//     return phoneRegex.test(phone);
// }

// // التعامل مع استجابة التسجيل
// function handleSignUpResponse(data) {
//     // For registration, we expect the server to return either "success" (request received) or "error" (there's a problem)
//     if (data.status === "success") {
//         Swal.fire({
//             icon: 'success',
//             title: 'Registration Request Submitted!',
//             text: 'Your account request has been received and is pending admin approval. You will be notified when approved.',
//             confirmButtonText: 'OK'
//         }).then(() => {
//             // Redirect user to confirmation or waiting page
//             window.location.href = "registration_confirmation.html";
//         });
//     } else {
//         Swal.fire({
//             icon: 'error',
//             title: 'Registration Error',
//             text: data.message || 'An error occurred while submitting your registration request. Please try again.',
//             confirmButtonText: 'OK'
//         });
//     }
// }

// // التعامل مع استجابة تسجيل الدخول
// function handleSignInResponse(data) {
//     if (data.status === "success") {
//         Swal.fire({
//             icon: 'success',
//             title: 'Login Successful!',
//             text: 'Redirecting to homepage...',
//             confirmButtonText: 'OK'
//         }).then(() => {
//             window.location.href = "TutorHome.html";
//         });
//     } else if (data.status === "pending") {
//         // If account is still pending approval
//         Swal.fire({
//             icon: 'info',
//             title: 'Account Under Review',
//             text: 'Your account is still under review by administration. You will be notified when approved.',
//             confirmButtonText: 'OK'
//         });
//     } else if (data.status === "rejected") {
//         // If account was rejected by administration
//         Swal.fire({
//             icon: 'error',
//             title: 'Account Rejected',
//             text: 'Unfortunately, your account request was rejected. Please contact administration for more information.',
//             confirmButtonText: 'OK'
//         });
//     } else {
//         // If credentials are incorrect
//         Swal.fire({
//             icon: 'error',
//             title: 'Login Error',
//             text: data.message || 'Email or password is incorrect. Please try again.',
//             confirmButtonText: 'OK'
//         });
//     }
// }
