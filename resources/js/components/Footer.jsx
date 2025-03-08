import React from 'react'
import { Link } from '@inertiajs/react'
import useZiggy from '../hooks/useZiggy';

const Footer = (props) => {
    const { route } = useZiggy();
    return (
        <>
            <section className='overflow-hidden mt-5' style={{ backgroundColor: '#F1F1F1' }}>
                <div className='main-container'>
                    <div className="row row-cols-2 row-cols-md-5 py-md-5 py-2 g-md-2 g-4">
                        <div className="col d-flex flex-column align-items-md-center align-items-start">
                            <i className="bi bi-currency-dollar fs-4 text-muted"></i>
                            <h6>Excellent deal</h6>
                            <small className='text-md-center text-start'>Always ensure the best value of your money by choosing among 50K authentic and original.</small>
                        </div>
                        <div className="col d-flex flex-column align-items-md-center align-items-start">
                            <i className="bi bi-truck fs-4 text-muted"></i>
                            <h6>Nation-wide Shipping</h6>
                            <small className='text-md-center text-start'>Always ensure the best value of your money by choosing among 50K authentic and original.</small>
                        </div>
                        <div className="col d-flex flex-column align-items-md-center align-items-start">
                            <i className="bi bi-credit-card-2-back fs-4 text-muted"></i>
                            <h6>Secure Payment</h6>
                            <small className='text-md-center text-start '>Always ensure the best value of your money by choosing among 50K authentic and original </small>
                        </div>
                        <div className="col d-flex flex-column align-items-md-center align-items-start">
                            <i className="bi bi-shield fs-4 text-muted"></i>
                            <h6>Shop with Trust</h6>
                            <small className='text-md-center text-start'>Always ensure the best value of your money by choosing among 50K authentic and original</small>
                        </div>
                        <div className="col d-flex flex-column align-items-md-center align-items-start">
                            <i className="bi bi-question-circle fs-4 text-muted"></i>
                            <h6>Help</h6>
                            <small className='text-md-center text-start'>Always ensure the best value of your money by choosing among 50K authentic and original </small>
                        </div>
                    </div>

                    <hr />

                    <div className="row row-cols-1 row-cols-md-4 g-4 py-md-5 justify-content-center align-items-start">
                        <div className="col">
                            <h6 className='mb-4 fw-bold'>Contact us</h6>
                            <div>
                                <small className=''>Call us 24/7</small>
                                <h4>{props.config.site_settings.phone}</h4>
                            </div>
                            <div className='mt-4'>
                                <small>Email Ua Anytime</small>
                                <h5>{props.config.site_settings.email}</h5>
                            </div>
                        </div>

                        <div className="col">
                            <h6 className='mb-4 fw-bold'>Quick links</h6>
                            {
                                props.footer_pages.map(page => (
                                    <Link key={page.id} href={route('ecommerce.page', page.slug)} className='text-decoration-none text-dark mb-2 d-block fw-light'><small>{ page.title}</small></Link>
                                ))
                            }
                        </div>

                        <div className="col">
                            <h6 className='mb-4 fw-bold'>Bussiness</h6>
                            <Link className='text-decoration-none text-dark mb-2 d-block fw-light'><small>Complain Box</small></Link>
                            <Link className='text-decoration-none text-dark mb-2 d-block fw-light'><small>Career</small></Link>
                            <Link className='text-decoration-none text-dark mb-2 d-block fw-light'><small>Shop</small></Link>
                            <Link className='text-decoration-none text-dark mb-2 d-block fw-light'><small>Certifications</small></Link>
                        </div>
                        <div className="col">
                            <h6 className='mb-4 fw-bold'>Find Us</h6>
                            <small className=''>{props.config.site_settings.address}</small>
                            <h6 className='mt-4 fw-bold'>Be Connected With Us</h6>
                            <div>
                                <a className='text-dark'target='_blank' href={props.config.site_settings.facebook}><i className="bi bi-facebook fs-4 me-3"></i></a>
                                <a className='text-dark' target="_blank" href={props.config.site_settings.linkedin}><i className="bi bi-linkedin fs-4 me-3"></i></a>
                                <a className='text-dark' target="_blank" href={props.config.site_settings.instagram}><i className="bi bi-instagram fs-4 me-3"></i></a>
                                <a className='text-dark' target="_blank" href={props.config.site_settings.twitter}><i className="bi bi-twitter fs-4 me-3"></i></a>
                                <a className='text-dark' target="_blank" href={props.config.site_settings.youtube}><i className="bi bi-youtube fs-4 me-3"></i></a>
                                <a className='text-dark' target="_blank" href={props.config.site_settings.google}><i className="bi bi-google fs-4 me-3"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className='py-3 overflow-hidden' style={{ backgroundColor: '#000000' }}>
                <div className='row justify-content-center align-items-center'>
                    <div className="col-12">
                        <img className='img-fluid' src="https://www.jadroo.com/_next/image?url=%2Fassets%2Fimg%2Fpayment-logo.png&w=1920&q=75" alt="" srcSet="" />
                        <small className='text-center text-white d-block my-2 '>© 2023 Zeo Tex Bd.com All Rights Reserved</small>
                    </div>
                </div>
            </section>
        </>
    )
}

export default Footer