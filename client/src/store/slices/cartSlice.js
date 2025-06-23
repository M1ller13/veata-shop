import { createSlice } from '@reduxjs/toolkit';

const getInitialCartItems = () => {
  try {
    return JSON.parse(localStorage.getItem('cartItems')) || [];
  } catch (error) {
    console.error('Error reading cart items from localStorage:', error);
    return [];
  }
};

const initialState = {
  items: getInitialCartItems(),
  total: 0,
  loading: false,
  error: null
};

const saveCartItems = (items) => {
  try {
    localStorage.setItem('cartItems', JSON.stringify(items));
  } catch (error) {
    console.error('Error saving cart items to localStorage:', error);
  }
};

const cartSlice = createSlice({
  name: 'cart',
  initialState,
  reducers: {
    addToCart: (state, action) => {
      const { id, name, price, image, quantity = 1 } = action.payload;
      const existingItem = state.items.find(item => item.id === id);

      if (existingItem) {
        existingItem.quantity += quantity;
      } else {
        state.items.push({
          id,
          name,
          price,
          image,
          quantity
        });
      }

      state.total = state.items.reduce(
        (total, item) => total + item.price * item.quantity,
        0
      );

      saveCartItems(state.items);
    },
    removeFromCart: (state, action) => {
      state.items = state.items.filter(item => item.id !== action.payload);
      state.total = state.items.reduce(
        (total, item) => total + item.price * item.quantity,
        0
      );
      saveCartItems(state.items);
    },
    updateQuantity: (state, action) => {
      const { id, quantity } = action.payload;
      const item = state.items.find(item => item.id === id);

      if (item) {
        item.quantity = quantity;
        state.total = state.items.reduce(
          (total, item) => total + item.price * item.quantity,
          0
        );
        saveCartItems(state.items);
      }
    },
    clearCart: (state) => {
      state.items = [];
      state.total = 0;
      try {
        localStorage.removeItem('cartItems');
      } catch (error) {
        console.error('Error clearing cart items from localStorage:', error);
      }
    }
  }
});

export const { addToCart, removeFromCart, updateQuantity, clearCart } = cartSlice.actions;

export default cartSlice.reducer; 