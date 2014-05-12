function showobj(x)
{
	if(x != null && x.className == "hidden") x.className = "shown";
}

function hideobj(x)
{                          
	if(x != null && x.className == "shown") x.className = "hidden";
}

function showid(name)
{
	showobj(document.getElementById(name));
}

function hideid(name)
{
	hideobj(document.getElementById(name));
}

function flip(name)
{
	x = document.getElementById(name);
	if(x == null) return;
	
//	x.style.display = x.style.display == "block" ? "none" : "block";

	if (x.className == "hidden") {
		// x.className = "shown";
		showobj(x);
	} else {
		// x.className = "hidden";
		hideobj(x);
	}

}
