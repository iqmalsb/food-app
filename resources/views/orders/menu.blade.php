@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Food Menu</div>


                <div class="card-body">
                    <form action="" method="">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" name="keyword" value="{{ request()->get('keyword') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Food ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Description</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cartList">                                
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between">
                        <div class="mx-2">
                            <button class="btn btn-danger" id="clearAllButton">Clear all</button>
                        </div>
                        <div class="mx-2">
                            {{-- <form action="{{ route('orders.confirm') }}" method="POST"> --}}
                                @csrf
                                <button class="btn btn-success" id="confirmOrderButton">Confirm Order</button>
                            {{-- </form>         --}}
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @foreach( $menu->chunk(2) as $chunk )
                        <div class="d-flex justify-content-evenly py-3">
                            @foreach($chunk as $item)
                                <div class="card col-3">
                                    <img src="{{ asset('/storage/'.$item->image) }}" class="card-img-top" alt="...">
                                    <div class="card-body mx-auto">
                                        <h5 class="card-title">{{ $item->name }}</h5>
                                        <h6 class="card-title">RM {{ $item->price }}</h6>
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" 
                                        data-bs-target="#orderModal" data-bs-food-id="{{ $item->id }}"
                                        data-bs-name="{{ $item->name }}" data-bs-price="{{ $item->price }}"
                                        data-bs-description="{{ $item->description }}">
                                            Add to Order
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <!-- Modal -->

                {{-- 
                    Input for Cart Items:
                    "food_id": food_id,
                    "name": food_name,
                    "description": description,
                    "price": price,
                    "quantity": quantity,
                    "additional_request": additional_request
                    --}}
                <div class="modal fade" id="orderModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Food Name</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('orders.insertItemToCart') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <p class="modal-item-id" id="food_id">Food ID</p>
                                    <input type="text" class="form-control" id="cart_id" name="cart_id" readonly></input>
                                    <input type="text" class="form-control" id="input_food_id" name="food_id" readonly></input>
                                    <p class="modal-item-name" id="food_name">Food Name</p>
                                    <p class="modal-item-description" id="food_description">Description: </p>
                                    <p class="modal-item-price" id="food_price">Price: </p>
                                    <label for="inputAdditionalRequest" class="form-label">Additional Request:</label>
                                    <input type="text" class="form-control" id="inputAdditionalRequest" name="additional_request"></input>
                                    <label for="input_quantity" class="form-label">Quantity:</label>
                                    <div class="d-flex justify-content-center">
                                        <div class="mx-2">
                                            <button class="btn btn-secondary" id="decQuantityBtn">-</button>
                                        </div>
                                        <div class="mx-2">
                                            <input type="text" class="form-control" id="input_quantity" name="quantity" size="1" readonly></input>
                                        </div>
                                        <div class="mx-2">
                                            <button class="btn btn-secondary" id="incQuantityBtn">+</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModal">Close</button>
                                    <button type="submit" class="btn btn-primary" id="addFood">Add Food</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- Modal Details --}}
                {{-- <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailsModalLabel">Food Name</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="modal-item-cart-id" id="details_item_id" hidden>Cart Item ID</p>
                                <p class="modal-item-id" id="details_food_id" hidden>Food ID</p>
                                <p class="modal-item-name" id="details_food_name">Food Name</p>
                                <p class="modal-item-description" id="details_food_description">Description: </p>
                                <p class="modal-item-price" id="details_food_price">Price: </p>
                                <label for="details_inputAdditionalRequest" class="form-label">Additional Request:</label>
                                <input type="text" class="form-control" id="details_inputAdditionalRequest" name="additional_request"></input>
                                <label for="details_quantity" class="form-label">Quantity:</label>
                                <div class="d-flex justify-content-center">
                                    <div class="mx-2">
                                        <button class="btn btn-secondary" id="details_decQuantityBtn">-</button>
                                    </div>
                                    <div class="mx-2">
                                        <input type="text" class="form-control" id="details_quantity" name="quantity" size="1" readonly></input>
                                    </div>
                                    <div class="mx-2">
                                        <button class="btn btn-secondary" id="details_incQuantityBtn">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="details_closeModal">Close</button>
                                <button type="button" class="btn btn-primary" id="updateFood">Update Food</button>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
{{-- <script>
    let data = sessionStorage.getItem("cart");
    let cartData = data ? JSON.parse(data) : []
    let quantity = 1
    let detailsQuantity = 1

    var cartListTable = document.getElementById("cartList")
    displayCartList()

    function displayCartList() {
        for (var i = 0; i < cartData.length; i++) {
            var row = cartListTable.insertRow(-1)
            var noCell = row.insertCell(0)
            var foodIDCell = row.insertCell(1)
            var foodNameCell = row.insertCell(2)
            var descriptionCell = row.insertCell(3)
            var quantityCell = row.insertCell(4)
            var actionsCell = row.insertCell(5)
            
            noCell.textContent = i + 1
            foodIDCell.textContent = cartData[i].food_id
            foodNameCell.textContent = cartData[i].name
            descriptionCell.textContent = cartData[i].additional_request
            quantityCell.textContent = cartData[i].quantity
            var detailsButton = document.createElement("BUTTON")
            detailsButton.className = "btn btn-info cart-item-info-button"
            detailsButton.innerHTML = "Details"
            detailsButton.setAttribute("id", 'detailscartData[' + i + ']')
            detailsButton.setAttribute("type", 'button')
            detailsButton.setAttribute("data-bs-toggle", 'modal')
            detailsButton.setAttribute("data-bs-target", '#detailsModal')
            detailsButton.setAttribute("data-bs-cart-item-id", i)
            detailsButton.setAttribute("data-bs-food-id", cartData[i].food_id)
            detailsButton.setAttribute("data-bs-name", cartData[i].name)
            detailsButton.setAttribute("data-bs-food-description", cartData[i].description)
            detailsButton.setAttribute("data-bs-price", cartData[i].price)
            detailsButton.setAttribute("data-bs-additional-request", cartData[i].additional_request)
            detailsButton.setAttribute("data-bs-quantity", cartData[i].quantity)
            actionsCell.appendChild(detailsButton)
            var deleteButton = document.createElement("BUTTON")
            deleteButton.className = "btn btn-warning cart-item-delete-button"
            deleteButton.innerHTML = "Delete"
            deleteButton.setAttribute("id", 'deletecartData[' + i + ']')
            deleteButton.setAttribute("onclick", 'removeItemFromCart(' + i + ')')
            actionsCell.appendChild(deleteButton)
        }
    }

    function updateCartList() {
        console.log("Kenapa for bawah ni tak boleh")
        console.log(cartData.length)
        for (var j = 1; j < cartData.length; j++) {
            console.log("Delete row no " + j)
            var row = cartListTable.deleteRow(0)
        }
        console.log("Tapi kenapa for bawah ni boleh")
        for (var i = 0; i < cartData.length; i++) {
            console.log("Tambah row no i = " + i)
            var row = cartListTable.insertRow(-1)
            var noCell = row.insertCell(0)
            var foodIDCell = row.insertCell(1)
            var foodNameCell = row.insertCell(2)
            var descriptionCell = row.insertCell(3)
            var quantityCell = row.insertCell(4)
            var actionsCell = row.insertCell(5)
            
            noCell.textContent = i+1
            foodIDCell.textContent = cartData[i].food_id
            foodNameCell.textContent = cartData[i].name
            descriptionCell.textContent = cartData[i].additional_request
            quantityCell.textContent = cartData[i].quantity
            var detailsButton = document.createElement("BUTTON")
            detailsButton.className = "btn btn-info cart-item-info-button"
            detailsButton.innerHTML = "Details"
            detailsButton.setAttribute("id", 'detailscartData[' + i + ']')
            detailsButton.setAttribute("type", 'button')
            detailsButton.setAttribute("data-bs-toggle", 'modal')
            detailsButton.setAttribute("data-bs-target", '#detailsModal')
            detailsButton.setAttribute("data-bs-cart-item-id", i)
            detailsButton.setAttribute("data-bs-food-id", cartData[i].food_id)
            detailsButton.setAttribute("data-bs-name", cartData[i].name)
            detailsButton.setAttribute("data-bs-food-description", cartData[i].description)
            detailsButton.setAttribute("data-bs-price", cartData[i].price)
            detailsButton.setAttribute("data-bs-additional-request", cartData[i].additional_request)
            detailsButton.setAttribute("data-bs-quantity", cartData[i].quantity)
            actionsCell.appendChild(detailsButton)
            var deleteButton = document.createElement("BUTTON")
            deleteButton.className = "btn btn-warning cart-item-delete-button"
            deleteButton.innerHTML = "Delete"
            deleteButton.setAttribute("id", 'deletecartData[' + i + ']')
            deleteButton.setAttribute("onclick", 'removeItemFromCart(' + i + ')')
            actionsCell.appendChild(deleteButton)
        }
        console.log("Settle delete semua item dalam cart")
    }

    function deleteCartList() {
        for (var j = 1; j < cartData.length; j++) {
            console.log("Delete row no " + j)
            var row = cartListTable.deleteRow(0)
        }
        sessionStorage.clear()
        data = sessionStorage.getItem("cart")
        console.log("value data")
        console.log(data)
        cartData = data ? JSON.parse(data) : []
        console.log("value cartData")
        console.log(cartData)
        console.log("Deh clear sessionstorage")
    }

    // document.getElementsByClassName('cart-item-delete-button').addEventListener("click", removeItemFromCart)

    // Order Modal
    const orderModal = document.getElementById('orderModal')
    orderModal.addEventListener('show.bs.modal', event => {
    // Button that triggered the modal
    const button = event.relatedTarget
    // Extract info from data-bs-* attributes
    const food_id = button.getAttribute('data-bs-food-id')
    const name = button.getAttribute('data-bs-name')
    const price = button.getAttribute('data-bs-price')
    const description = button.getAttribute('data-bs-description')
    // If necessary, you could initiate an AJAX request here
    // and then do the updating in a callback.
    //
    // Update the modal's content.
    const modalTitle = orderModal.querySelector('.modal-title')
    const modal_food_id = document.getElementById('food_id')
    const modal_input_food_id = document.getElementById('input_food_id')
    const modalBodyName = orderModal.querySelector('.modal-item-name')
    const modalBodyDescription = document.getElementById('food_description')
    const modalBodyPrice = orderModal.querySelector('.modal-item-price')
    const modalBodyQuantity = document.getElementById('input_quantity')

    modalTitle.textContent = `Make new order of ${name}`
    modal_food_id.textContent = `${food_id}`
    modal_input_food_id.value = `${food_id}`
    modalBodyName.textContent = `${name}`
    modalBodyDescription.textContent = `Description: ${description}`
    modalBodyPrice.textContent = `Price: RM${price}`
    modalBodyQuantity.value = `${quantity}`
    })

    //     Details Modal
    const detailsModal = document.getElementById('detailsModal')
    detailsModal.addEventListener('show.bs.modal', event => {
    // Button that triggered the modal
    const button = event.relatedTarget
    // Extract info from data-bs-* attributes
    const id = button.getAttribute('data-bs-cart-item-id')
    const food_id = button.getAttribute('data-bs-food-id')
    const name = button.getAttribute('data-bs-name')
    const price = button.getAttribute('data-bs-price')
    const description = button.getAttribute('data-bs-food-description')
    const additionalRequest = button.getAttribute('data-bs-additional-request')
    const quantity = button.getAttribute('data-bs-quantity')
    // If necessary, you could initiate an AJAX request here
    // and then do the updating in a callback.
    //
    // Update the modal's content.
    const modalTitle = detailsModal.querySelector('.modal-title')
    const modalItemID = detailsModal.querySelector('.modal-item-cart-id')
    const modalBodyID = detailsModal.querySelector('.modal-item-id')
    const modalBodyName = detailsModal.querySelector('.modal-item-name')
    const modalBodyDescription = document.getElementById('details_food_description')
    const modalAdditionalRequest = document.getElementById('details_inputAdditionalRequest')
    const modalBodyPrice = detailsModal.querySelector('.modal-item-price')
    const modalBodyQuantity = document.getElementById('details_quantity')

    modalTitle.textContent = `Details | ${name}`
    modalItemID.textContent = `${id}`
    modalBodyID.textContent = `${food_id}`
    modalBodyName.textContent = `${name}`
    //Change to additional request
    modalBodyDescription.textContent = `Description: ${description}`
    modalBodyPrice.textContent = `Price: RM${price}`
    modalBodyQuantity.value = `${quantity}`
    modalAdditionalRequest.value = `${additionalRequest}`
    detailsQuantity = modalBodyQuantity.value
    })

    document.getElementById("decQuantityBtn").addEventListener("click", decQuantity)
    document.getElementById("incQuantityBtn").addEventListener("click", incQuantity)
    document.getElementById("details_decQuantityBtn").addEventListener("click", detailsDecQuantity)
    document.getElementById("details_incQuantityBtn").addEventListener("click", detailsIncQuantity)

    function decQuantity() {
        if (quantity > 0) {
            quantity--
            document.getElementById('input_quantity').value = `${quantity}`
        }
    }

    function incQuantity() {
        quantity++
        document.getElementById('input_quantity').value = `${quantity}`
    }

    function detailsDecQuantity() {
        if (detailsQuantity > 0) {
            detailsQuantity--
            document.getElementById('details_quantity').value = `${detailsQuantity}`
        }
    }

    function detailsIncQuantity() {
        detailsQuantity++
        document.getElementById('details_quantity').value = `${detailsQuantity}`
    }

    orderModal.addEventListener('hidden.bs.modal', event => {
        document.getElementById("inputAdditionalRequest").value = ""
        quantity = 1
    })
    
    // Get Food Details and Add Food to Cart
    // 1. Food ID
    // 2. Additional Request
    // 3. Quantity of Food (Buat For Loop i)
    document.getElementById("addFood").addEventListener("click", addToCart);
    document.getElementById("updateFood").addEventListener("click", updateDetails);
    // document.getElementById("addFood").addEventListener('click', () => {
    //     orderModal.modal('hide');
    //     alert('Nice, you triggered this alert message!', 'success')
    // })

    
    function addToCart() {
        const food_id =  document.getElementById('food_id').textContent
        const food_name = document.getElementById('food_name').textContent
        const description = document.getElementById('food_description').textContent.slice(12)
        const price = document.getElementById('food_price').textContent.slice(9)
        const quantity = document.getElementById('input_quantity').value
        const additional_request =  document.getElementById('inputAdditionalRequest').value
        
        const cartItem = {
            "food_id": food_id,
            "name": food_name,
            "description": description,
            "price": price,
            "quantity": quantity,
            "additional_request": additional_request
        }

        console.log("sebelum push")
        cartData.push(cartItem)
        console.log("selepas push")
        const cart = JSON.stringify(cartData)
        sessionStorage.setItem("additional_request", additional_request)
        
        alert('Nice, you triggered this alert message!', 'success')
        console.log("cart data is")
        console.log(cartData)
        updateCartList()
    }
    
    function removeItemFromCart(i) {
        console.log("removeItemFromCart function for item " + i)
        alert('Nice, you removed an item from the cart', 'danger')
        var row = cartListTable.deleteRow(i)
        var row = cartListTable.deleteRow(0)
        cartData.splice(i, 1)
        const cart = JSON.stringify(cartData)
        sessionStorage.setItem("cart", cart)
        updateCartList()
    }
    
    // Reset Additional Request Value to empty string
    document.getElementById("closeModal").addEventListener("click", resetCart)
    // document.getElementById("confirmOrderButton").addEventListener("click", confirmOrder)
    
    document.getElementById("clearAllButton").addEventListener("click", emptyCartList)
    
    function resetCart() {
        quantity = 1
    }
    
    function emptyCartList() {
        var row = cartListTable.deleteRow(0)
        deleteCartList()
    }

    function updateDetails() {
        const food_id =  document.getElementById('food_id').textContent
        const food_name = document.getElementById('food_name').textContent
        const description = document.getElementById('details_food_description').textContent.textContent.slice(12)
        const price = document.getElementById('food_price').textContent.slice(9)
        const quantity = document.getElementById('details_quantity').value
        const additional_request =  document.getElementById('details_inputAdditionalRequest').value
        const i = document.getElementById('details_item_id').textContent
        cartData[i] = {
            "food_id": food_id,
            "name": food_name,
            "description": description,
            "price": price,
            "quantity": quantity,
            "additional_request": additional_request
        }
        const cart = JSON.stringify(cartData)
        sessionStorage.setItem("cart", cart)
        
        alert('Nice, you triggered this UPDATE alert message!', 'success')
        console.log("cart data is")
        console.log(cartData)
        var row = cartListTable.deleteRow(0)
        updateCartList()
    }

    // function confirmOrder() {
    //     alert('Your order has been confirmed')
    //     const sessionStorageData = sessionStorage.getItem('cart');
    //     // $.ajax({
    //     //     headers: {
    //     //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     //     },
    //     //     method: "POST",
    //     //     url: "{{ route('orders.confirm') }}",
    //     //     dataType: "stjsonring",
    //     //     data:{
    //     //         sessionStorageData: sessionStorageData,
    //     //         '_token' : '{{ csrf_token() }}'
    //     //     },
    //     //     success: function(data) {
    //     //         alert(data);
    //     //         console.log("Data is = " + data)
    //     //     }
    //     // });


    // }
    // let sessionStorageData = sessionStorage.getItem('cart');
    
    // $.ajaxSetup({
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     }
    // });
    
    // $(".btn-submit").click(function(e){
    //     e.preventDefault();
    //     let submitData = JSON.parse(sessionStorageData)
    //     console.log("Value of submitData = ");

    //     console.log(sessionStorageData);
    //     console.log(submitData);
    //     console.log(submitData[0]);
    //     console.log(submitData[1]);
    //     console.log(submitData[2]);

    //     $.ajax({
    //         type:'POST',
    //         url:"{{ route('orders.confirm') }}",
    //         data:{submitData},
    //         success:function(data){
    //             if($.isEmptyObject(data.error)){
    //                 alert(data.success);
    //                 location.reload();
    //             }else{
    //                 printErrorMsg(data.error);
    //             }
    //         }
    //     });
    // });

</script> --}}
@endsection
