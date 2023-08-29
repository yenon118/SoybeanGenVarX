
function downloadUserManual() {
	let downloadAnchorNode = document.createElement('a');
	downloadAnchorNode.setAttribute("href", "php/downloadUserManual.php?File=GenVarX_User_Manual.pdf");
	document.body.appendChild(downloadAnchorNode); // required for firefox
	downloadAnchorNode.click();
	downloadAnchorNode.remove();
}
