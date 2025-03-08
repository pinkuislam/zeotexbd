import React, { useEffect, useRef } from "react";
import { Link, useForm } from "@inertiajs/react";
import useZiggy from "../hooks/useZiggy";
import { useState } from "react";

const MiniCartComponent = (props) => {
    const { route } = useZiggy();
    const { delete: destroy } = useForm();

    const onDelete = (item) => {
        destroy(route('ecommerce.cart.destroy', item.product.product_id), { preserveScroll: true });
    }

    const [isOpen, setIsOpen] = useState(false);
    const dropdownRef = useRef(null);

    useEffect(() => {
        const handleOutsideClick = (event) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setIsOpen(false);
            }
        };

        document.addEventListener('click', handleOutsideClick);

        return () => {
            document.removeEventListener('click', handleOutsideClick);
        };
    }, []);

    const toggleDropdown = () => {
        setIsOpen(!isOpen);
    };

    return (
        <>
            <div className="d-inline-block dropdown dropdown-large me-md-2 me-2 order-md-1 order-0" ref={dropdownRef}>
                {/* <a onClick={() => toggleDropdown()} type='button' className="text-dark dropdown-toggle position-relative cart-link dropdown-toggle-nocaret">
                    <small className="alert-count">{props.cart ? Object.values(props.cart.items).length : 0}</small>
                    <i className="bi bi-cart4 fs-2 te"></i>
                </a> */}
                <Link type="button" href={route('ecommerce.cart.index')} className="text-dark cart-link">
                    <small className="alert-count">{props.cart ? Object.values(props.cart.items).length : 0}</small>
                    <i className="bi bi-cart4 fs-2 te"></i>
                </Link>
                <div className={`dropdown-menu dropdown-menu-end  ${isOpen ? 'show' : ''}`} style={{ right: '0px' }}>
                    <div className="cart-header">
                        <p className="cart-header-title mb-0">{props.cart && Object.values(props.cart.items).length} ITEMS</p>
                        <p className="cart-header-clear ms-auto mb-0">
                            <Link className='text-decoration-none text-light' onClick={() => toggleDropdown(false)} href={route('ecommerce.cart.index')}>VIEW CART</Link>
                        </p>
                    </div>
                    <div className="cart-list">
                        {props.cart && Object.values(props.cart.items).map(item => (
                            <div className="dropdown-item" key={item.product.product_id}>
                                <div className="d-flex align-items-center">
                                    <div className="flex-grow-1">
                                        <Link onClick={() => toggleDropdown()} className="text-decoration-none text-dark" key={item.product.product_id} href={route('ecommerce.products.show', item.product.product.other_info.slug)}>
                                            <h6 className="cart-product-title ">{item.product.product.name.length>20 ? item.product.product.name.substring(0, 25) + "..." : item.product.product.name}</h6>
                                            <p className="cart-product-price">{item.quantity} X &#2547;{parseInt(item.product.sale_price)}</p>
                                        </Link>
                                    </div>
                                    <div className="position-relative">
                                        <button type="button" className="cart-product-cancel position-absolute" onClick={() => onDelete(item)}>
                                            <i className="bi bi-x-circle"></i>
                                        </button>
                                        <div className="cart-product">
                                            <img src={item.product.product.other_info.image_url}
                                                alt="product image" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="text-center cart-footer d-flex align-items-center">
                        <h5 className="mb-0">TOTAL</h5>
                        <h5 className="mb-0 ms-auto">&#2547;{props.cart?.subtotal || 0}</h5>
                    </div>
                    <div className="d-grid p-3 border-top">
                        <Link  onClick={() => toggleDropdown(false)} href={route('ecommerce.checkout.index')} className="btn btn-primary btn-ecomm">CHECKOUT</Link>
                    </div>
                </div>
            </div>
        </>
    );
}



export default MiniCartComponent;



