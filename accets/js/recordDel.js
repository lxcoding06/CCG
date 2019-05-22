// JavaScript Document
function toggle(source, mg) {

    var getInputs = document.getElementsByTagName("input");
    for (var i = 0, max = getInputs.length; i < max; i++) {
        if (getInputs[i].type === 'checkbox')
            console.log(source.checked);
//console.log(document.getElementById("foo").value);
        if (source.checked == true)
        {
            getInputs[i].checked = true;
            console.log("hi");
            document.getElementById("recycle").style.display = "block";
        } else
        {
            getInputs[i].checked = false;
            document.getElementById("recycle").style.display = "none";
        }
    }

    data = values();

}

function values()
{
    var val = $('input:checked').map(function () {
        return this.value;
    }).get();
    //alert(val);
    return val;
}

function callme(strng, page, h_id)
{
    //console.log(page);
    //alert(strng+","+page+","+h_id);
    data = values();
    if (data == "")
    {
        document.getElementById("recycle").style.display = "none";
    } else
    {
        document.getElementById("recycle").style.display = "block";
        if (!strng) {
            delRow();
        }
    }
}