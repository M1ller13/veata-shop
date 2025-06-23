const mongoose = require('mongoose');

const ProductSchema = new mongoose.Schema({
    name: {
        type: String,
        required: [true, 'Название товара обязательно'],
        trim: true,
        maxlength: [100, 'Название не может быть длиннее 100 символов']
    },
    description: {
        type: String,
        required: [true, 'Описание товара обязательно'],
        trim: true
    },
    category: {
        type: String,
        required: [true, 'Категория товара обязательна'],
        trim: true
    },
    price: {
        type: Number,
        required: [true, 'Цена товара обязательна'],
        min: [0, 'Цена не может быть отрицательной']
    },
    stock: {
        type: Number,
        required: [true, 'Количество товара обязательно'],
        min: [0, 'Количество не может быть отрицательным'],
        default: 0
    },
    unit: {
        type: String,
        required: [true, 'Единица измерения обязательна'],
        trim: true
    },
    dimensions: {
        type: String,
        trim: true
    },
    weight: {
        type: String,
        trim: true
    },
    material: {
        type: String,
        trim: true
    },
    brand: {
        type: String,
        trim: true
    },
    specifications: {
        type: String,
        trim: true
    },
    features: [{
        type: String,
        trim: true
    }],
    images: [{
        type: String,
        trim: true
    }],
    isActive: {
        type: Boolean,
        default: true
    },
    createdAt: {
        type: Date,
        default: Date.now
    },
    updatedAt: {
        type: Date,
        default: Date.now
    }
}, {
    timestamps: true
});

// Add text index for search functionality
ProductSchema.index({
    name: 'text',
    description: 'text',
    category: 'text',
    brand: 'text',
    material: 'text'
});

// Pre-save middleware to update the updatedAt field
ProductSchema.pre('save', function(next) {
    this.updatedAt = Date.now();
    next();
});

// Static method to get all categories
ProductSchema.statics.getCategories = async function() {
    return this.distinct('category');
};

// Instance method to check if product is in stock
ProductSchema.methods.isInStock = function() {
    return this.stock > 0;
};

// Instance method to update stock
ProductSchema.methods.updateStock = async function(quantity) {
    if (this.stock + quantity < 0) {
        throw new Error('Недостаточно товара на складе');
    }
    this.stock += quantity;
    return this.save();
};

module.exports = mongoose.model('Product', ProductSchema); 