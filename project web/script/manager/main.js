window.onload = function () {
    const itemList = document.getElementById("itemList");
    const itemID = document.querySelector("input[placeholder='Item ID']");
    const supplierID = document.querySelector("input[placeholder='Supplier ID']");
    const itemName = document.getElementById("itemName");
    const itemPrice = document.getElementById("itemPrice");
    const itemImage = document.querySelector(".card-img-top");
    const itemText = document.querySelector(".card-text");

    updateUI();
    itemList.addEventListener("change", updateUI);

    // Define the function to update the UI
    function updateUI() {
        // Get the selected item's ID from the dropdown
        const selectedItemID = itemList.value - 1;

        let itemIDArray = ["12345678", "23456789", "34567890", "45678901", "56789012", "67890123", "78901234"];
        let supplierIDArray = ["12345678", "98765432", "87654321", "76543210", "65432109", "54321098", "43210987"];
        let itemNameArray = ["Local Potato (1 pc)", "Local Carrot (1 pc)", "Local Apple 1 (1 pc)", "Local Apple 2 (1 pc)", "Local Orange (1 pc)", "Local Mango (1 pc)", "Local Lemon (1 pc)"];
        let itemPriceArray = ["50", "25", "30", "30", "20", "50", "10"];

        itemID.value = itemIDArray[selectedItemID];
        supplierID.value = supplierIDArray[selectedItemID];
        itemName.value = itemNameArray[selectedItemID];
        itemPrice.value = itemPriceArray[selectedItemID];
        itemImage.src = `../images/${selectedItemID + 1}.png`;
        itemText.innerText = itemNameArray[selectedItemID];
    }
};

