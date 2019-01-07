$(document).ready(function () {
    console.log(" je suis ok ok  !!!!!!!!");

    var xhr = new XMLHttpRequest();
    var url = "https://api.jsonbin.io/b/5c1b7c5f412d482eae522b7f";
    
    xhr.onreadystatechange = function () {

        if (xhr.readyState == 4 && xhr.status == 200) {

            var jsdonData = JSON.parse(xhr.responseText);
            showData(jsdonData);

            console.log(jsdonData);

        }

    }
});