import React, { useState } from 'react'
import PageWrapper from '../../layouts/PageWrapper'
import { useForm, usePage } from '@inertiajs/react';
import useZiggy from "../../hooks/useZiggy";
import { useDropzone } from 'react-dropzone'

const CheckoutPage = (props) => {

    const { cart, shippings } = props;
    const { errors } = usePage().props
    const { route } = useZiggy();
    const [area, setArea] = useState(0)

    const { post, processing, data, setData, reset } = useForm({
        name: '',
        phone: '',
        area: '',
        address: '',
        images: []
    });

    const handleArea = (event) => {
        setData('area', event.target.value);
        setArea(event.target.selectedOptions[0].getAttribute('data-rate'));
    };

    const {
        acceptedFiles,
        fileRejections,
        getRootProps,
        getInputProps
    } = useDropzone({
        accept: {
            'image/jpeg': [],
            'image/jpg': [],
            'image/png': []
        }
    });

    const acceptedFileItems = acceptedFiles.map(file => (
        <li key={file.path}>
            {file.path} - {file.size} bytes
        </li>
    ));

    const onSubmit = (e) => {
        e.preventDefault();
        data.images = acceptedFiles;
        post(route('ecommerce.order.store'), data, {
            preserveScroll: true,
            onSuccess: () => reset()
        });
    };

    return (
        <PageWrapper>
            <section className="py-4">
                <div className={`main-container ${processing ? 'processing' : ''}`}>
                    <div className="shop-cart">
                        <div className="row">
                            <div className="col-12 col-xl-8">
                                <div className="shop-cart-list mb-3 p-3">
                                    {cart && Object.values(cart.items).map(item => (
                                        <div key={item.product.product_id}>
                                            <div className="row align-items-center g-3">
                                                <div className="col-12 col-lg-6">
                                                    <div className="d-flex justify-content-start align-items-center gap-3">
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
                                            </div>
                                            <hr />
                                        </div>
                                    ))}
                                </div>
                            </div>


                            <div className="col-12 col-xl-4">
                                <div className="checkout-form p-3 bg-light">
                                    <div className="card rounded-0 border bg-transparent shadow-none">
                                        <div className="card-body">
                                            <p className="fs-5">Estimate Shipping</p>
                                            <div className="my-3 border-top"></div>
                                            <form action="">
                                                <div className="mb-3">
                                                    <label className="form-label">Name</label>
                                                    <input value={data.name} onChange={(e) => setData('name', e.target.value)} name="name" type="text" className={`form-control shadow-none ${errors.name ? 'is-invalid' : ''}`} />
                                                    {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                                                </div>
                                                <div className="mb-3">
                                                    <label className="form-label">Phone</label>
                                                    <input value={data.phone} onChange={(e) => setData('phone', e.target.value)} name="phone" type="tel" className={`form-control shadow-none ${errors.phone ? 'is-invalid' : ''}`} />
                                                    {errors.phone && <div className="invalid-feedback">{errors.phone}</div>}
                                                </div>
                                                <div className="mb-3">
                                                    <label className="form-label">Area</label>
                                                    <select value={data.area} onChange={handleArea} id="areaSelect" name="area" className={`form-control rounded shadow-none ${errors.area ? 'is-invalid' : ''}`} aria-label="Default select example">
                                                        <option>Select Area</option>
                                                        {
                                                            shippings.map(shipping => (
                                                                <option key={shipping.id} data-rate={shipping.rate} value={shipping.id}>{shipping.area} -- {shipping.rate}</option>
                                                            ))
                                                        }
                                                    </select>
                                                    {errors.area && <div className="invalid-feedback">{errors.area}</div>}
                                                </div>
                                                <div className="mb-3">
                                                    <label htmlFor="exampleFormControlTextarea1" className="form-label">Address</label>
                                                    <textarea value={data.value} onChange={(e) => setData('address', e.target.value)} name="address" type="text" className="form-control shadow-none" id="exampleFormControlTextarea1" rows="3"></textarea>
                                                </div>

                                                <section className='mt-3'>
                                                    <div {...getRootProps({ className: 'dropzone' })}>
                                                        <input {...getInputProps()} />
                                                        <p className='p-3 border rounded bg-white' style={{ cursor: 'pointer', fontSize: '12px' }}>Upload Your Sofa Images (optional)</p>
                                                    </div>
                                                    <aside>
                                                        <small style={{ fontSize: '12px' }}>Accepted files</small>
                                                        <ul><small style={{ fontSize: '12px' }}>{acceptedFileItems}</small></ul>
                                                    </aside>
                                                </section>
                                            </form>
                                        </div>
                                    </div>
                                    <div className="card rounded-0 border bg-transparent mb-0 shadow-none">
                                        <div className="card-body">
                                            <p className="mb-2">Subtotal: <span className="float-end">&#2547;{cart?.subtotal || 0}</span></p>
                                            <p className="mb-2"> Shipping: <span className="float-end">&#2547;{area ? parseInt(area) : 0}</span></p>
                                            <div className="my-3 border-top"></div>
                                            <h5 className="mb-0">Order Total: <span className="float-end">&#2547;{parseInt(cart?.subtotal) + parseInt(area) || 0}</span></h5>
                                            <div className="my-4"></div>
                                            <div className="d-grid">
                                                <button onClick={onSubmit} className="btn btn-primary btn-ecomm rounded-0">Place Order</button>
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

export default CheckoutPage