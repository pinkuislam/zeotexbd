import React from 'react'
import { Link, useForm } from "@inertiajs/react";
import { Button } from 'react-bootstrap';
import Card from 'react-bootstrap/Card';
import Col from 'react-bootstrap/Col';
import useZiggy from '../hooks/useZiggy';



const ProductCard = (props) => {

    const { route } = useZiggy();
    const { record } = props;

    let discount = 0;

    if (record.product_items[0]?.old_price) {
        discount = ([(record.product_items[0]?.old_price - record.product_items[0]?.sale_price) / record.product_items[0]?.old_price] * 100);
    }
    const { post } = useForm({
        product_id: record.id,
        quantity: 1,
        color_id: record.product_items[0]?.color_id,
        size_id: record.product_items[0]?.size_id
    });

    const onSubmit = (e) => {
        e.preventDefault();
        post(route('ecommerce.cart.store'), { preserveScroll: true });
    }

    return (
        <Col>
            <Link className='text-decoration-none' href={route('ecommerce.products.show', record.other_info.slug)}>
                <Card className='p-md-3 rounded-0 product-card border-none h-100'>
                    <p className='discount-text'>
                        {
                            discount > 0 ? <span className='d-block float-end py-0 px-md-2 px-1 rounded-0 bg-primary text-white' variant="primary">{parseInt(discount)}%</span> : ''
                        }
                    </p>
                    <Card.Img className='rounded-0 ' variant="top" src={record.other_info?.image_url} />
                    <Card.Body className='px-0 pt-0'>
                        <p className='text-center cart-btn-wrapper m-0 py-1'>
                            <Button className='cart-btn' variant="primary" onClick={onSubmit}>Add to cart</Button>
                        </p>
                        
                        <hr className='mt-0' />
                        <Card.Title className='product-card-title'>{record.name.length > 25 ? record.name.substring(0, 40) + "" : record.name}</Card.Title>
                        <Card.Text className='product-card-price'>
                            <span className='text-primary me-2 fw-semibold'>{parseInt(record.product_items[0]?.sale_price)}</span>
                            {
                                record.product_items[0]?.old_price ? <span className='text-muted text-decoration-line-through' >{parseInt(record.product_items[0]?.old_price)}</span> : ''
                            }
                        </Card.Text>
                    </Card.Body>
                </Card>
            </Link>
        </Col>
    )
}

export default ProductCard
