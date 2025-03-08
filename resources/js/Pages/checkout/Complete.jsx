import { Link } from '@inertiajs/react'
import useZiggy from '../../hooks/useZiggy';

const CheckoutCompletePage = (props) => {
    const { order } = props;
    const { route } = useZiggy();
    return (
        <section className="py-4">
            <div className="container">
                <div className="card py-3 mt-sm-3 shadow border-0">
                    <div className="card-body text-center">
                        <h2 className="h4 pb-3">Thank you for your order!</h2>
                        <p className="fs-sm mb-2">Your order has been placed and will be processed as soon as possible.</p>
                        <p className="fs-sm mb-2">Make sure you make note of your order number, which is <span className="fw-medium">{ order.serial_number}</span></p>
                        <p className="fs-sm">You will be receiving an email shortly with confirmation of your order. <u>You can now:</u>
                        </p>
                        <Link className="btn btn-light rounded-0 mt-3 me-3" href={route('ecommerce.home')}>Go back shopping</Link>
                        <Link className="btn btn-light rounded-0 mt-3" href={route('ecommerce.tracking.index')}><i className="bi bi-geo-alt"></i>Track order</Link>
                    </div>
                </div>
            </div>
        </section>
    )
}

export default CheckoutCompletePage