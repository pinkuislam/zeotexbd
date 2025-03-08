import { useEffect, useState } from "react";
import { Row, Stack } from "react-bootstrap";
import { Head, Link, useForm, usePage } from "@inertiajs/react";
import useZiggy from "../../hooks/useZiggy";
import Form from 'react-bootstrap/Form';
import { Swiper, SwiperSlide } from 'swiper/react';
import Lightbox from 'react-18-image-lightbox';
import 'react-18-image-lightbox/style.css';
import ProductCard from "../../components/ProductCard";
import toastr from "toastr";
import PageWrapper from "../../layouts/PageWrapper";
import axios from "axios";
// import ReactPlayer from "react-player";
import ReactPlayer from 'react-player/youtube'



// Import Swiper styles
import 'swiper/swiper-bundle.css';
// import 'swiper/css';
import 'swiper/css/free-mode';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';
import { EffectFade, Thumbs, Pagination, Scrollbar, Mousewheel } from 'swiper/modules';




const ProductReview = ({ product }) => {
    const { route } = useZiggy();
    const { errors } = usePage().props
    const { post, processing, data, setData, reset } = useForm({
        name: '',
        phone: '',
        message: '',
        rating: 0,
    });

    const onSubmit = (e) => {
        e.preventDefault();
        post(route('ecommerce.products.review', product.id), { preserveScroll: true, onSuccess: () => reset() });
    }

    return (
        <div className="add-review border">
            <Head>
                {product.other_info.meta_title && <title>{product.other_info.meta_title}</title>}
                {product.other_info.meta_description && <meta name="description" content={product.other_info.meta_description} />}
                {product.other_info.meta_keywords && <meta name="keywords" content={product.other_info.meta_keywords} />}
            </Head>
            <div className="form-body p-3">
                <h4 className="mb-4">Write a Review</h4>
                <form>
                    <div className="mb-3">
                        <label className="form-label">Name</label>
                        <input value={data.name} onChange={(e) => setData('name', e.target.value)} type="text" className={`form-control rounded-0 shadow-none ${errors.name ? 'is-invalid' : ''}`} />
                        {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                    </div>
                    <div className="mb-3">
                        <label className="form-label">Phone</label>
                        <input value={data.phone} onChange={(e) => setData('phone', e.target.value)} type="text" className={`form-control rounded-0 shadow-none ${errors.phone ? 'is-invalid' : ''}`} />
                        {errors.phone && <div className="invalid-feedback">{errors.phone}</div>}
                    </div>
                    <div className="mb-3">
                        <label className="form-label">Rating</label>
                        <select value={data.rating} onChange={(e) => setData('rating', e.target.value)} className={`form-control rounded-0 shadow-none ${errors.rating ? 'is-invalid' : ''}`} aria-label="Default select example">
                            <option value="">Choose Rating</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        {errors.rating && <div className="invalid-feedback">{errors.rating}</div>}
                    </div>
                    <div className="mb-3">
                        <label className="form-label">Messege</label>
                        <textarea value={data.message} onChange={(e) => setData('message', e.target.value)} className={`form-control rounded-0 shadow-none ${errors.message ? 'is-invalid' : ''}`} />
                        {errors.message && <div className="invalid-feedback">{errors.message}</div>}
                    </div>
                    <div className="d-grid">
                        <button onClick={onSubmit} type="button" className="btn btn-dark btn-ecomm">Submit a Review</button>
                    </div>
                </form>
            </div>
        </div>
    )
}

const ProductsShow = (props) => {
    const { product, relatedProduct, return_policy } = props;
    const { route } = useZiggy();
    const [thumbsSwiper, setThumbsSwiper] = useState(null);
    const [videoIsOpen, setVideoIsOpen] = useState(!!product.other_info.video_link);
    const [lightboxIsOpen, setLightboxIsOpen] = useState(false);
    const [lightboxIndex, setLightboxIndex] = useState(0);
    const [variantData, setVariantData] = useState({
        sale_price: product.product_items[0].sale_price,
        old_price: product.product_items[0].old_price,
        barcode: product.product_items[0].barcode,
    });

    const { post, processing, data, setData } = useForm({
        product_id: product.id,
        quantity: 1,
        color_id: product.product_items[0].color_id,
        size_id: product.product_items[0].size_id,
        action: 'cart'
    });

    const openLightbox = () => {
        if (!videoIsOpen) {
            setLightboxIsOpen(true);
        }
    };

    const handleLightboxIndex = (index) => {
        setLightboxIndex(index);
        setVideoIsOpen(false)
    }

    const breakpoints = {
        // When window width is >= 768px
        768: {
            direction: 'vertical',
            slidesPerView: 6,
            spaceBetween: 10,
        },
        // When window width is < 768px
        0: {
            direction: 'horizontal',
            slidesPerView: 4,
            spaceBetween: 7,
        }
    };

    const onSubmit = (e) => {
        e.preventDefault();
        post(route('ecommerce.cart.store'), { preserveScroll: true });
    }

    const handleBuy = (e) => {
        e.preventDefault();
        setData(data => ({ ...data, action: 'buy' }));

    }

    useEffect(() => {
        if (data.action === 'buy') {
            post(route('ecommerce.cart.store'));
        }
    }, [data])

    const handleIncrease = (e) => {
        e.preventDefault();
        setData(data => ({ ...data, quantity: data.quantity + 1 }));
    }

    const handleDecrease = (e) => {
        e.preventDefault();
        if (data.quantity > 1) {
            setData(data => ({ ...data, quantity: data.quantity - 1 }));
        }
    }

    useEffect(() => {
        props.onProccessing?.(processing);
    }, [processing]);

    useEffect(() => {
        if (data.color_id || data.size_id) {
            axios.get(route('ecommerce.product.variant'), {
                params: {
                    id: product.id,
                    color: data.color_id,
                    size: data.size_id
                }
            })
                .then((response) => {
                    if (response.data.data == null) {
                        toastr.error('This variant product not available');
                    }
                    setVariantData(response.data.data);
                })
                .catch((error) => {
                    console.error('Error fetching variant data:', error);
                });
        }
    }, [data.color_id, data.size_id]);

    const handleColorChange = (event) => {
        setData(data => ({ ...data, submit: false, color_id: parseInt(event.target.value) }));
    };

    const handleSizeChange = (event) => {
        setData(data => ({ ...data, submit: false, size_id: parseInt(event.target.value) }));
    };


    return (
        <PageWrapper>
            <section className="w-100 py-3 border-bottom border-top d-none d-md-flex" style={{ backgroundColor: '#EAEAEA' }}>
                <div className="main-container px-2">
                    <Link className="text-decoration-none me-2" href={route('ecommerce.home')}><small>Home <i className="fa-solid fa-angles-right fa-sm "></i></small></Link>
                    <Link className="text-decoration-none me-2" href={route('ecommerce.category', product.other_info.category.parent.parent.slug)}><small>{product.other_info.category.parent.parent.name} <i className="fa-solid fa-angles-right fa-sm"></i></small></Link>
                    <Link className="text-decoration-none me-2" href={route('ecommerce.category', product.other_info.category.parent.slug)}><small>{product.other_info.category.parent.name} <i className="fa-solid fa-angles-right fa-sm"></i></small></Link>
                    <Link className="text-decoration-none me-2" href={route('ecommerce.category', product.other_info.category.slug)}><small>{product.other_info.category.name} <i className="fa-solid fa-angles-right fa-sm"></i></small></Link>
                    <small className="d-md-inline-block d-none">{product.name}</small>
                </div>
            </section>

            <section className="main-container overflow-hidden mt-3" >
                <div>
                    <div className="row g-4">
                        <div className="col-md-6 py-md-3 ">
                            <div className="row g-3">
                                <div className="col-md-2 order-md-0 order-1">
                                    <Swiper
                                        direction={'vertical'}
                                        breakpoints={breakpoints}
                                        onSwiper={setThumbsSwiper}
                                        scrollbar={false}
                                        mousewheel={true}
                                        watchSlidesProgress={false}
                                        modules={[EffectFade, Thumbs, Scrollbar, Mousewheel]}
                                        className="mySwiper"
                                    >
                                        {product.other_info.video_link && (
                                            <SwiperSlide onClick={() => setVideoIsOpen(true)} className="border border-dark rounded-1">
                                                <i className="bi bi-camera-video fs-2 text-danger"></i>
                                            </SwiperSlide>
                                        )}
                                        {product.other_images.map((image, index) => (
                                            <SwiperSlide onClick={() => handleLightboxIndex(index)} key={index} className="rounded-1">
                                                <img className="swiper-thumb-img p-1" src={image.image_url} alt={`Thumb ${index}`} style={{ objectFit: 'fill' }} />
                                            </SwiperSlide>
                                        ))}
                                    </Swiper>
                                </div>

                                <div className="col-md-10 order-md-1 order-0">
                                    <Swiper
                                        effect={'fade'}
                                        thumbs={{ swiper: thumbsSwiper }}
                                        spaceBetween={10}
                                        modules={[EffectFade, Thumbs]}
                                        className="mySwiper2 border rounded-1"
                                    >
                                        {product.other_info.video_link && (
                                            <SwiperSlide>
                                                <ReactPlayer
                                                    className="p-2 img-fluid"
                                                    url={product.other_info.video_link}
                                                    title="YouTube video player"
                                                    frameBorder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                    controls={true}
                                                    playing
                                                    width="100%"
                                                    height="100%"
                                                />
                                            </SwiperSlide>
                                        )}
                                        {product.other_images.map((image, index) => (
                                            <SwiperSlide onClick={() => openLightbox()} key={index}>
                                                <img className="p-2 img-fluid" src={image.image_url} alt={`Main ${index}`} style={{ cursor: 'pointer' }} />
                                            </SwiperSlide>
                                        ))}
                                    </Swiper>


                                    {lightboxIsOpen && (
                                        <Lightbox
                                            mainSrc={product.other_images[lightboxIndex].image_url} // Current image URL
                                            nextSrc={product.other_images[(lightboxIndex + 1) % product.other_images.length].image_url} // Next image URL
                                            prevSrc={product.other_images[(lightboxIndex + product.other_images.length - 1) % product.other_images.length].image_url} // Previous image URL
                                            onCloseRequest={() => setLightboxIsOpen(false)} // Close the lightbox when requested
                                            onMovePrevRequest={() => setLightboxIndex((lightboxIndex + product.other_images.length - 1) % product.other_images.length)} // Move to the previous image
                                            onMoveNextRequest={() => setLightboxIndex((lightboxIndex + 1) % product.other_images.length)} // Move to the next image
                                        />
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="col-md-6 py-3">
                            <div>
                                <h5 className="fw-semibold">{product.name}</h5>
                                <Stack direction="horizontal" gap={1} className="star-rating">
                                    {[...Array(5)].map((star, index) => {
                                        return (
                                            <span key={index} className="star text-warning fs-5">&#9733;</span>
                                        );
                                    })}
                                    <div className="vr my-auto d-inline-block bg-primary opacity-100" style={{ height: '10px', width: '2px' }} />
                                    <small className="text-primary fw-normar">5 Rating</small>
                                </Stack>
                                <h6 className="text-success my-2 fw-normal"><i className="bi bi-fire"></i>10 orders in the last 15 hours</h6>
                                <small>Brand: <small className="text-primary">Zeo Tex Bd</small></small>
                                <h5 className="my-2">
                                    <span className='fs-6 fw-semibold text-primary me-2'>&#2547; {variantData?.sale_price ? parseInt(variantData?.sale_price) : "0.00"}</span> <span className='fs-6 text-muted text-decoration-line-through'>{variantData?.old_price ? parseInt(variantData?.old_price) : ""}</span>
                                </h5>
                                <small><small className="fw-semibold">Sold By:</small> <small className="text-primary">Zeo Tex Bd</small></small>
                                <div className="lh-md my-2">
                                    <small className="d-block fw-semibold"><i className="bi bi-check-lg fs-6 me-1"></i>90% Cash on Delivery</small>
                                    <small className="d-block fw-semibold"><i className="bi bi-check-lg fs-6 me-1"></i>Delivery within 15 - 30 Days</small>
                                    <small className="d-block fw-semibold"><i className="bi bi-check-lg fs-6 me-1"></i>Imported Product</small>
                                    <small className="d-block fw-semibold"><i className="bi bi-check-lg fs-6 me-1"></i>Delivery All Over Bangladesh</small>
                                    <small className="d-block fw-semibold"><i className="bi bi-check-lg fs-6 me-1"></i><a href="#" className="text-decoration-none">See Return Policy</a></small>
                                </div>
                                <p className="mt-3"><span className="bg-primary rounded px-2 text-white py-1 me-3">SKU:</span><span className="border rounded px-2 py-1">{variantData ? variantData?.barcode : '----'}</span></p>

                                <div className="mt-4 ">
                                    <Form.Select id="colorSelect" onChange={handleColorChange} defaultValue={data.color_id ? data.color_id : ''} aria-label="Default select example" className="shadow-none rounded-0 category-select d-inline-block me-4 mb-md-0 mb-4">
                                        <option>Color</option>
                                        {
                                            product.product_items.map(cl => (
                                                cl.color_id ? (
                                                    <option key={`${cl.color_id}-${cl.size_id}`} value={cl.color_id}>{cl.color?.name}</option>
                                                ) : (
                                                    ''
                                                )
                                            ))
                                        }
                                    </Form.Select>
                                    <Form.Select id="sizeSelect" onChange={handleSizeChange} defaultValue={data.size_id ? data.size_id : ''} aria-label="Default select example" className="shadow-none rounded-0 category-select  d-inline-block">
                                        <option>Size</option>
                                        {
                                            product.product_items.map(si => (
                                                si.size_id ? (
                                                    <option key={`${si.color_id}-${si.size_id}`} value={si.size_id}>{si.size?.name}</option>
                                                ) : (
                                                    ''
                                                )
                                            ))
                                        }
                                    </Form.Select>
                                </div>

                                <div className="row my-md-4 my-3">
                                    <div className="col-md-4">
                                        <button className="btn btn-light rounded-0" onClick={handleDecrease}>-</button>
                                        <span className="border mx-1 d-inline-block rounded-0" style={{ width: '20%', padding: '6.5px 55px' }}>{data.quantity}</span>
                                        <button className="btn btn-light rounded-0" onClick={handleIncrease}>+</button>
                                    </div>
                                    <div className="col-md-5 offset-md-0 my-md-0 my-4">
                                        <button onClick={handleBuy} type="button" className="btn btn-primary me-3">Buy Now</button>
                                        <button onClick={onSubmit} type="button" className="btn btn-primary">Add to cart</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {/* product-description */}
                <div className="mx-3">
                    <div className="row mt-md-5 mt-2 align-items-baseline  justify-content-md-around jsutify-content-center g-4">

                        {/* Review */}
                        <div className="col-md-8 col-12 border rounded px-md-2 px-1">
                            <ul className="nav nav-tabs my-3 px-0 mx-0" id="myTab" role="tablist">
                                <li className="nav-item" role="presentation">
                                    <button className="nav-link active text-dark" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Description</button>
                                </li>
                                <li className="nav-item" role="presentation">
                                    <button className="nav-link text-dark text-wrap" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Review</button>
                                </li>
                                <li className="nav-item" role="presentation">
                                    <button className="nav-link text-dark text-wrap" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Return Policy</button>
                                </li>
                            </ul>
                            <div className="tab-content mx-2" id="myTabContent">
                                <div className="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabIndex="0">
                                    <p dangerouslySetInnerHTML={{ __html: product.other_info.description }}></p>
                                </div>
                                <div className="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabIndex="0">
                                    <div className="row mb-5">
                                        <div className="col-md-6 col-xxl-7">
                                            <div className="product-review">
                                                <h5 className="mb-4">{product.reviews.length} Reviews For The Product</h5>
                                                <div className="review-list">
                                                    {
                                                        product.reviews.map(review => (
                                                            <div key={review.id}>
                                                                <div className="d-flex align-items-start">
                                                                    <div className="review-content w-100">
                                                                        <div className="rates cursor-pointer fs-6">
                                                                            {[...Array(review.rating)].map((star, index) => {

                                                                                return (
                                                                                    <i key={index} className="bi bi-star-fill text-warning me-1"></i>
                                                                                );
                                                                            })}
                                                                        </div>
                                                                        <div className="d-flex justify-content-between align-items-center mb-2">
                                                                            <h6 className="mb-0">{review.name}</h6>
                                                                            <p className="mb-0 ">{new Date(review.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                                                        </div>
                                                                        <p>{review.message}</p>
                                                                    </div>
                                                                </div>
                                                                <hr />
                                                            </div>
                                                        ))
                                                    }
                                                </div>
                                            </div>
                                        </div>

                                        <div className="col-md-6 col-xxl-5">
                                            <ProductReview product={product}></ProductReview>
                                        </div>
                                    </div>
                                </div>
                                <div className="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabIndex="0">
                                    <p>
                                        {
                                            return_policy ? <div dangerouslySetInnerHTML={{ __html: return_policy.details }}></div> : ''
                                        }
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Related Products */}
                        <div className="col-md-3 col-12  border rounded">
                            <h6 className="fw-semibold border-bottom px-0 py-4">Related Products</h6>
                            <Row xs={2} md={1} lg={1} xxl={1} className="g-4 my-4">
                                {relatedProduct?.map((record) => (<ProductCard key={record.id} record={record.product}></ProductCard>))}
                            </Row>
                        </div>
                    </div>
                </div>
            </section >
        </PageWrapper>
    )
}

export default ProductsShow;
