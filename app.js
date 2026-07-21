let items = [];

// عند تحميل الصفحة تلقائياً
document.addEventListener("DOMContentLoaded", () => {
    // 1. ضبط التاريخ الحالي
    const today = new Date();
    const formattedDate = `${today.getFullYear()}/${today.getMonth() + 1}/${today.getDate()}`;
    const dateElem = document.getElementById("invoice_date");
    if (dateElem) dateElem.innerText = formattedDate;
    
    // 2. توليد رقم مرجعي
    const randomRef = 'SP-' + Math.floor(100000 + Math.random() * 900000);
    const refElem = document.getElementById("invoice_ref");
    if (refElem) refElem.innerText = randomRef;
});

// زيادة ونقصان الكمية
function incrementQty() {
    const qtyInput = document.getElementById("item_qty");
    if (qtyInput) {
        qtyInput.value = parseInt(qtyInput.value || 1) + 1;
    }
}

function decrementQty() {
    const qtyInput = document.getElementById("item_qty");
    if (qtyInput && parseInt(qtyInput.value) > 1) {
        qtyInput.value = parseInt(qtyInput.value) - 1;
    }
}

// تحديث بيانات المشتري فورياً عند الكتابة
function updateBuyerInfo() {
    const name = document.getElementById("buyer_name")?.value;
    const phone = document.getElementById("buyer_phone")?.value;
    const address = document.getElementById("buyer_address")?.value;

    if (document.getElementById("preview_buyer_name")) {
        document.getElementById("preview_buyer_name").innerText = name || "-";
    }
    if (document.getElementById("preview_buyer_phone")) {
        document.getElementById("preview_buyer_phone").innerText = phone || "-";
    }
    if (document.getElementById("preview_buyer_address")) {
        document.getElementById("preview_buyer_address").innerText = address || "-";
    }
}

// تحديث المبلغ الإجمالي الكلي يدوياً عند الكتابة
function updateTotalAmount() {
    const manualTotal = document.getElementById("manual_total_price")?.value;
    const totalElem = document.getElementById("preview_total_amount");
    if (totalElem) {
        totalElem.innerText = manualTotal ? manualTotal : "0 د.ع";
    }
}

// إضافة مادة جديدة للجدول (تمت المعالجة لمنع تحديث الصفحة)
function addItemToInvoice(event) {
    if (event) event.preventDefault();

    const nameInput = document.getElementById("item_name");
    const snInput = document.getElementById("item_sn");
    const warrantyInput = document.getElementById("item_warranty");
    const specInput = document.getElementById("item_spec");
    const qtyInput = document.getElementById("item_qty");

    const name = nameInput ? nameInput.value.trim() : "";
    const sn = snInput ? snInput.value.trim() : "";
    const warranty = warrantyInput ? warrantyInput.value.trim() : "";
    const spec = specInput ? specInput.value.trim() : "";
    const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;

    if (!name) {
        alert("يرجى إدخال اسم المادة أولاً!");
        return;
    }

    const item = {
        id: Date.now(),
        name: name,
        sn: sn,
        warranty: warranty || "لا يوجد",
        spec: spec,
        qty: qty
    };

    items.push(item);
    renderItems();

    // تفريغ حقول الإدخال بعد الإضافة
    if (nameInput) nameInput.value = "";
    if (snInput) snInput.value = "";
    if (warrantyInput) warrantyInput.value = "";
    if (specInput) specInput.value = "";
    if (qtyInput) qtyInput.value = "1";
}

// حذف مادة من الفاتورة
function deleteItem(id) {
    items = items.filter(item => item.id !== id);
    renderItems();
}

// إعادة رسم جدول المواد في الفاتورة
function renderItems() {
    const tbody = document.getElementById("invoice_items_table");
    if (!tbody) return;
    
    tbody.innerHTML = "";

    items.forEach(item => {
        const tr = document.createElement("tr");
        tr.className = "border-b text-gray-700";

        tr.innerHTML = `
            <td class="p-2">
                <div class="font-bold text-gray-800">${item.name}</div>
                ${item.spec ? `<div class="text-xs text-gray-500">${item.spec}</div>` : ''}
                ${item.sn ? `<div class="text-xs font-mono text-blue-600">S/N: ${item.sn}</div>` : ''}
            </td>
            <td class="p-2 text-center font-bold">${item.qty}</td>
            <td class="p-2 text-center text-sm font-semibold text-emerald-600">${item.warranty}</td>
            <td class="p-2 text-center print:hidden">
                <button type="button" onclick="deleteItem(${item.id})" class="text-red-500 hover:text-red-700 font-bold">✕</button>
            </td>
        `;

        tbody.appendChild(tr);
    });
}

// أزرار الطباعة والتصدير
function printInvoice() {
    window.print();
}

function saveAndExportPDF() {
    window.print();
}
