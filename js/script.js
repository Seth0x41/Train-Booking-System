function logFunc() {
    document.getElementById("logContainer").style.display="block";
}
function logFunc2() {
    document.getElementById("logContainer").style.display="none";
}
function func1() {
    document.getElementById("logIn").style.display="none";
    document.getElementById("signUp").style.display="block";
}
function func2() {
    document.getElementById("logIn").style.display="block";
    document.getElementById("signUp").style.display="none";
}

function toggleForm(formType) {
    var addForm = document.getElementById('add_form');
    var deleteForm = document.getElementById('delete_form');
  
    if (formType === 'add') {
      addForm.style.display = 'block';
      deleteForm.style.display = 'none';
    } else if (formType === 'delete') {
      addForm.style.display = 'none';
      deleteForm.style.display = 'block';
    }
  }