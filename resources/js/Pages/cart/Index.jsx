import React, { useEffect, useState } from 'react'
import { Link, useForm } from "@inertiajs/react";
import PageWrapper from "../../layouts/PageWrapper";
import useZiggy from "../../hooks/useZiggy";


export const CartItem = (props) => {

    const { route } = useZiggy();
    const { item } = props;

    const { post, processing, data, setData } = useForm({
        submit: false,
        product_id: item.product.product_id,
        color_id: item.product.color_id,
        size_id: item.product.size_id,
        quantity: 1,
    });

    const { delete: destroy } = useForm();

    const handleIncrease = (e) => {
        e.preventDefault();
        setData(data => ({ ...data, submit: true, quantity: 1 }));
    }

    const handleDecrease = (e) => {
        e.preventDefault();
        setData(data => ({ ...data, submit: true, quantity: -1 }));
    }

    const onDelete = (e) => {
        e.preventDefault();
        destroy(route('ecommerce.cart.destroy', item.product.product_id), { preserveScroll: true });
    }

    useEffect(() => {
        if (data.submit) {
            setData(data => ({ ...data, submit: false }));
            post(route('ecommerce.cart.store'), { preserveScroll: true });
        }
    }, [data]);

    useEffect(() => {
        props.onProccessing?.(processing);
    }, [processing]);

    return (
        <div className="row align-items-center justify-content-start g-3">
            <div className="col-12 col-lg-6">
                <div className="d-flex align-items-center justify-content-lg-start justify-content-between gap-3">
                    <div className="cart-img text-center text-lg-start">
                        <img src={item.product.product.other_info.image_url} width="130" alt="" />
                    </div>
                    <div className="cart-detail text-center text-lg-start">
                        <h6 className="mb-2">{item.product.product.name}</h6>
                        <p className="mb-0">Size: <span>{item.product.size ? item.product.size.name : ''}</span></p>
                        <p className="mb-2">Color: <span>{item.product.color ? item.product.color.name : ''}</span></p>
                        <h5 className="mb-0">{item.quantity} X &#2547;{parseInt(item.product.sale_price)}</h5>
                    </div>
                </div>
            </div>

            <div className="col-12 col-lg-6">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <button className="btn btn-light rounded-0" onClick={handleDecrease}>-</button>
                        <span className="border rounded-0 mx-2 d-inline-block" style={{ width: '20%', padding: '6.5px 50px' }}>{item.quantity}</span>
                        <button className="btn btn-light rounded-0" onClick={handleIncrease}>+</button>
                    </div>
                    <div>
                        <a onClick={onDelete} href={route('ecommerce.cart.destroy', item.product.product_id)} className="btn btn-outline-danger rounded-0 btn-ecomm ms-lg-5 ms-3"><i className="bi bi-x"></i>Remove</a>
                    </div>
                </div>
            </div>
        </div>
    );
}


const ProductCart = ({ cart }) => {
    const { route } = useZiggy();
    const [processing, setProcessing] = useState(false);
    return (
        <PageWrapper>
            <section className="py-4">
                <div className={`main-container ${processing ? 'processing' : ''}`}>
                    <div className="shop-cart">
                        <div className="row">
                            <div className="col-12 col-xl-8">
                                <div className="shop-cart-list mb-3 p-3">
                                    {cart && Object.values(cart.items).map(item => (
                                        <React.Fragment key={item.product.id}>
                                            <CartItem item={item} onProccessing={setProcessing} />
                                            <hr />
                                        </React.Fragment>
                                    ))}
                                    <div className="d-lg-flex align-items-center gap-2">
                                        <a href={route('ecommerce.home')} className="btn btn-primary btn-ecomm rounded-0"><i className='bx bx-shopping-bag '></i> Continue Shopping</a>
                                    </div>
                                </div>
                            </div>
                            <div className="col-12 col-xl-4">
                                <div className="checkout-form p-3 bg-light">
                                    {/* <div className="card rounded-0 border bg-transparent shadow-none">
                                        <div className="card-body">
                                            <p className="fs-5">Apply Discount Code</p>
                                            <div className="input-group">
                                                <input type="text" className="form-control rounded-0 shadow-none" placeholder="Enter discount code" />
                                                <button className="btn btn-dark btn-ecomm" type="button">Apply</button>
                                            </div>
                                        </div>
                                    </div> */}
                                    <div className="card rounded-0 border bg-transparent mb-0 shadow-none">
                                        <div className="card-body">
                                            <p className="mb-2">Subtotal: <span className="float-end">${cart?.subtotal || 0}</span></p>
                                            <p className="mb-2"> Shipping: <span className="float-end">--</span></p>
                                            <p className="mb-0">Discount: <span className="float-end">--</span></p>
                                            <div className="my-3 border-top"></div>
                                            <h5 className="mb-0">Order Total: <span className="float-end">&#2547;{(cart?.subtotal || 0)}</span></h5>
                                            <div className="my-4"></div>
                                            <div className="d-grid">
                                                <Link href={route('ecommerce.checkout.index')} className="btn btn-primary btn-ecomm rounded-0">Proceed to Checkout</Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </PageWrapper>
    )
}

export default ProductCart
