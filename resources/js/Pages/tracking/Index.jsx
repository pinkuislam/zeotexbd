
import { useForm } from '@inertiajs/react';
import useZiggy from '../../hooks/useZiggy';
import toastr from "toastr";


const OrderTrackingPage = (props) => {
    const { trackingOrder } = props;
    const { route } = useZiggy();
    const { get, processing, data, setData, reset } = useForm({
        tracking_number: ''
    });

    const onSubmit = (e) => {
        e.preventDefault();
        get(route('ecommerce.tracking.index', data.tracking_number), { preserveScroll: true, onSuccess: () => reset() });
    }


    return (
        <section className="py-4 main-container">
            <div className="container-fluid">
                <div className='d-flex justify-content-md-between justify-content-center align-items-center flex-wrap'>
                    <h6 className="mb-0 order-md-0 order-1 mt-md-0 mt-4">Order ID: {trackingOrder ? trackingOrder?.serial_number : 'Order Not Found'}</h6>
                    <div className='d-flex justify-content-center align-items-center order-md-1 order-0'>
                        <input value={data.tracking_number} onChange={(e) => setData('tracking_number', e.target.value)} type="text" className=" form-control rounded-0 shadow-none border-end-0" />
                        <button onClick={onSubmit} type="button" className="btn btn-light rounded-0 border">Search</button>
                    </div>
                </div>
                <hr />
                <div className="row row-cols-1 row-cols-lg-4 rounded-4 gx-3 m-0 border ">
                    <div className="col p-4 text-center border-end">
                        <h6 className="mb-1">Order By:</h6>
                        <small className="mb-0">{trackingOrder?.name }</small> <br />
                        <small className="mb-0">{trackingOrder?.phone }</small>
                    </div>
                    <div className="col p-4 text-center border-end">
                        <h6 className="mb-1">Shipping BY:</h6>
                        {/* <p className="mb-0">BLUEDART | +91-9910XXXX</p> */}
                    </div>
                    <div className="col p-4 text-center border-end">
                        <h6 className="mb-1">Status:</h6>
                        <p className="mb-0"> {trackingOrder?.status==='Shipped'?'Picked by the courier' : '' }</p>
                    </div>
                    <div className="col p-4 text-center">
                        <h6 className="mb-1">Tracking #:</h6>
                        <p className="mb-0">{trackingOrder?.serial_number}</p>
                    </div>
                </div>
                <div className="mt-3"></div>
                <div className="checkout-payment">
                    <div className="card bg-transparent rounded-0 shadow-none border-0">
                        <div className="card-body">
                            <div className="steps steps-light">
                                <a className={`step-item ${trackingOrder?.status === 'Placed' ? 'active' : ''}`}>
                                    <div className="step-progress"><span className="step-count"><i className="bi bi-person"></i></span>
                                    </div>
                                    <div className="step-label">Order Placed</div>
                                </a>
                                <a className={`step-item ${trackingOrder?.status === 'Processing' ? 'active' : ''}`}>
                                    <div className="step-progress"><span className="step-count"><i className="bi bi-check2"></i></span>
                                    </div>
                                    <div className="step-label">Order Confirmed</div>
                                </a>
                                <a className={`step-item ${trackingOrder?.status === 'Shipped' ? 'active' : ''}`}>
                                    <div className="step-progress"><span className="step-count"><i className="bi bi-person-circle"></i></span>
                                    </div>
                                    <div className="step-label">On the way</div>
                                </a>
                                <a className={`step-item ${trackingOrder?.status === 'Delivered' ? 'active' : ''}`}>
                                    <div className="step-progress"><span className="step-count"><i className="bi bi-truck"></i></span>
                                    </div>
                                    <div className="step-label">Delivered</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    )
}

export default OrderTrackingPage
