// تاريخ اليوم التلقائي للفاتورة
document.getElementById('invoice_date').innerText = new Date().toLocaleDateString('ar-EG');

// مصفوفة لتخزين المواد المضافة في الجلسة الحالية
let invoiceItems = [];

// 1. ربط حقول العميل بالتحديث الفوري
document.getElementById('buyer_name').addEventListener('input', function(e) {
    document.getElementById('preview_buyer_name').innerText = e.target.value || '-';
});
document.getElementById('buyer_phone').addEventListener('input', function(e) {
    document.getElementById('preview_buyer_phone').innerText = e.target.value || '-';
});
document.getElementById('buyer_address').addEventListener('input', function(e) {
    document.getElementById('preview_buyer_address').innerText = e.target.value || '-';
});

// 2. التحكم في العداد (الكمية)
function incrementQty() {
    let qtyInput = document.getElementById('item_qty');
    qtyInput.value = parseInt(qtyInput.value) + 1;
}

function decrementQty() {
    let qtyInput = document.getElementById('item_qty');
    if (parseInt(qtyInput.value) > 1) {
        qtyInput.value = parseInt(qtyInput.value) - 1;
    }
}

// 3. إضافة مادة إلى مصفوفة الفاتورة
// ابحث عن دالة addItemToInvoice وتأكد من أخذ السعر كعدد صحيح (إلغاء الكسور إن وجدت)
function addItemToInvoice() {
    const name = document.getElementById('item_name').value.trim();
    // استخدام Math.round لجعل السعر صحيحاً بدون أي بوينتات
    const price = Math.round(parseFloat(document.getElementById('item_price').value));
    const sn = document.getElementById('item_sn').value.trim();
    const spec = document.getElementById('item_spec').value.trim();
    const qty = parseInt(document.getElementById('item_qty').value);

    if (!name || isNaN(price) || price <= 0) {
        alert("يرجى إدخال اسم المادة وسعرها بشكل صحيح.");
        return;
    }

    const item = {
        id: Date.now(),
        name: name,
        price: price,
        sn: sn,
        spec: spec,
        qty: qty,
        total: price * qty // سينتج تلقائياً رقم صحيح
    };

    invoiceItems.push(item);
    renderInvoiceItems();
    clearItemFormInputs();
}

// 4. إفراغ خانات إدخال المادة فقط بعد الإضافة لتسهيل إضافة المادة التالية
function clearItemFormInputs() {
    document.getElementById('item_name').value = '';
    document.getElementById('item_price').value = '';
    document.getElementById('item_sn').value = '';
    document.getElementById('item_spec').value = '';
    document.getElementById('item_qty').value = '1';
}

// 5. رسم وتحديث جدول المواد في الجانب الأيسر
function renderInvoiceItems() {
    const tbody = document.getElementById('invoice_items_table');
    tbody.innerHTML = '';
    let grandTotal = 0;

    invoiceItems.forEach((item) => {
        grandTotal += item.total;
        
        // تنسيق السعر والإجمالي بدون بوينتات ومع فواصل الآلاف
        const formattedPrice = item.price.toLocaleString('en-US'); // أو 'ar-IQ' إذا كنت تفضل الأرقام العربية الشرقية ١،٢٣٤
        const formattedTotal = item.total.toLocaleString('en-US');

        tbody.innerHTML += `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-2">
                    <span class="font-medium text-gray-800">${item.name}</span>
                    ${item.spec ? `<div class="text-xs text-gray-500">${item.spec}</div>` : ''}
                    ${item.sn ? `<div class="text-xs text-blue-600 font-mono">S/N: ${item.sn}</div>` : ''}
                </td>
                <td class="p-2 text-center font-bold">${item.qty}</td>
                <td class="p-2 text-left font-mono">${formattedPrice} د.ع</td>
                <td class="p-2 text-left font-mono font-bold text-gray-900">${formattedTotal} د.ع</td>
                <td class="p-2 text-center print:hidden">
                    <button onclick="removeItem(${item.id})" class="text-red-500 hover:text-red-700 font-bold">❌</button>
                </td>
            </tr>
        `;
    });

    // تحديث المجموع الإجمالي بالأسفل بدون بوينتات وبصيغة الدينار العراقي
    document.getElementById('preview_total_amount').innerText = `${grandTotal.toLocaleString('en-US')} د.ع`;
}

// 6. حذف مادة من الفاتورة
function removeItem(id) {
    invoiceItems = invoiceItems.filter(item => item.id !== id);
    renderInvoiceItems();
}

// 7. زر الطباعة المباشرة
function printInvoice() {
    if (invoiceItems.length === 0) {
        alert("الفاتورة فارغة! يرجى إضافة مادة واحدة على الأقل قبل الطباعة.");
        return;
    }
    window.print(); // يفتح نافذة الطباعة الخاصة بالمتصفح ويطبق ستايل الطباعة
}

// 8. حفظ الفاتورة في قاعدة البيانات عن طريق الـ PHP وتوليد رقم مرجعي
function saveAndExportPDF() {
    const buyerName = document.getElementById('buyer_name').value.trim();
    const buyerPhone = document.getElementById('buyer_phone').value.trim();
    const buyerAddress = document.getElementById('buyer_address').value.trim();
    
    if (!buyerName) {
        alert("يرجى إدخال اسم المشتري لحفظ الفاتورة.");
        return;
    }
    if (invoiceItems.length === 0) {
        alert("يرجى إضافة مادة واحدة على الأقل قبل الحفظ.");
        return;
    }

    const totalAmount = invoiceItems.reduce((acc, item) => acc + item.total, 0);

    // تجميع كل البيانات لإرسالها بالـ AJAX
    const requestData = {
        buyer_name: buyerName,
        buyer_phone: buyerPhone,
        buyer_address: buyerAddress,
        total_amount: totalAmount,
        items: invoiceItems
    };

    // إرسال البيانات بطلب POST إلى ملف save_invoice.php
    fetch('save_invoice.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // تحديث الرقم المرجعي للفاتورة باليسار بالرقم الجديد القادم من قاعدة البيانات
            document.getElementById('invoice_ref').innerText = 'INV-' + String(data.invoice_id).padStart(5, '0');
            alert(data.message);
            // فتح خيارات الطباعة لحفظها كـ PDF بعد الحفظ الناجح
            window.print();
        } else {
            alert("حدث خطأ أثناء الحفظ: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("فشل الاتصال بالخادم.");
    });
}