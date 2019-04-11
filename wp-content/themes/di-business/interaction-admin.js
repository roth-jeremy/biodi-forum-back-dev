document.addEventListener("DOMContentLoaded", function (event) {
    var currentPlant;
    let template =
        `<form>
<input type="text" value="">
<input type="text">
</form>`;
    const btnModify = document.getElementById("modify");
    btnModify.onclick = e => {
        console.log(myplants.find);
        //currentPlant = new plant()
    };

});
function plant(id,name, description) {
    this.id = id;
    this.name = name;
    this.description = description;

}