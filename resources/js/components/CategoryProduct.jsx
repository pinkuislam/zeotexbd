import React, { useState } from 'react'
import { Row } from 'react-bootstrap';
import ProductCard from './ProductCard';

const CategoryProduct = (props) => {
    const { title, products } = props;
    const [itemsToShow, setItemsToShow] = useState(12);
    const handleLoadMore = () => {
        setItemsToShow(itemsToShow + 6);
    };
    return (
        <div>
            <div className="my-md-5 my-4 bg-primary text-white overflow-hidden">
                <h1 className="ms-lg-4 ms-1 category-title mt-2">{title}</h1>
            </div>
            <Row xs={2} md={6} className="g-4">
                {products.slice(0, itemsToShow).map((record) => (
                    <ProductCard key={record.id} record={record} />
                ))}
            </Row>
            {itemsToShow < products.length && (
                <button
                    onClick={() => {
                        handleLoadMore();
                    }}
                    type="button"
                    className="btn btn-primary d-block mx-auto mt-4 rounded-0 px-5"
                >
                    Load More
                </button>
            )}
        </div>
    )
}

export default CategoryProduct