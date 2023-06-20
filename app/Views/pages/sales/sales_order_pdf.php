<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Welcome to CodeIgniter</title>
</head>

<body>
    <div id="container">
        <h1>Maintain Sales Order</h1>
        <div id="body">
            <table>
                <tr>
                    <td>Sales Order No</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['contract_no'] ?></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['date'] ?></td>
                </tr>
                <tr>
                    <td>Category</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['category'] ?></td>
                </tr>
                <tr>
                    <td>Type</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['type'] ?></td>
                </tr>
                <tr>
                    <td>Buyer</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['customer_code'] ?></td>
                </tr>
                <tr>
                    <td>Buyer Name</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['customer_name'] ?></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['address'] ?></td>
                </tr>
                <tr>
                    <td>Product</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['product'] ?></td>
                </tr>
                <tr>
                    <td>Nama Product</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['product_name'] ?></td>
                </tr>
                <tr>
                    <td>Quantity</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['quantity'] ?></td>
                </tr>
                <tr>
                    <td>Unit of Measure</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['uom'] ?></td>
                </tr>
                <tr>
                    <td>Contract Price</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['contract_price'] ?></td>
                </tr>
                <tr>
                    <td>Currency</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['currency'] ?></td>
                </tr>
                <tr>
                    <td>Delivery Condition</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['delivery_condition'] ?></td>
                </tr>
                <tr>
                    <td>Quality Parameter</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['quality_parameter'] ?></td>
                </tr>
                <tr>
                    <td>Parameter</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['parameter'] ?></td>
                </tr>
                <tr>
                    <td>Terms of Payment</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['top'] ?></td>
                </tr>
                <tr>
                    <td>WBS Element</td>
                    <td class="py-2">:</td>
                    <td><?= @$_GET['wbs_element'] ?></td>
                </tr>
            </table>
            <hr>
            <p>Coal Monitoring System</p>
            <p>PT. Energi Batubara Lestari</p>
        </div>
    </div>
</body>

</html>