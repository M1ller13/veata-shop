import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || 'http://localhost:5000/api';

const getInitialUser = () => {
  try {
    return JSON.parse(localStorage.getItem('user')) || null;
  } catch (error) {
    console.error('Error reading user from localStorage:', error);
    return null;
  }
};

const getInitialToken = () => {
  try {
    return localStorage.getItem('token') || null;
  } catch (error) {
    console.error('Error reading token from localStorage:', error);
    return null;
  }
};

const initialState = {
  user: getInitialUser(),
  token: getInitialToken(),
  isAuthenticated: !!getInitialUser(),
  loading: false,
  error: null
};

const saveAuthData = (token, user) => {
  try {
    if (token) localStorage.setItem('token', token);
    if (user) localStorage.setItem('user', JSON.stringify(user));
  } catch (error) {
    console.error('Error saving auth data to localStorage:', error);
  }
};

const clearAuthData = () => {
  try {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  } catch (error) {
    console.error('Error clearing auth data from localStorage:', error);
  }
};

// Register user
export const register = createAsyncThunk(
  'auth/register',
  async (userData, { rejectWithValue }) => {
    try {
      const response = await axios.post(`${API_URL}/users/register`, userData);
      saveAuthData(response.data.token, response.data.user);
      return response.data;
    } catch (error) {
      return rejectWithValue(error.response?.data?.msg || 'Registration failed');
    }
  }
);

// Login user
export const login = createAsyncThunk(
  'auth/login',
  async (userData, { rejectWithValue }) => {
    try {
      const response = await axios.post(`${API_URL}/users/login`, userData);
      saveAuthData(response.data.token, response.data.user);
      return response.data;
    } catch (error) {
      return rejectWithValue(error.response?.data?.msg || 'Login failed');
    }
  }
);

// Get current user
export const getCurrentUser = createAsyncThunk(
  'auth/getCurrentUser',
  async (_, { rejectWithValue, getState }) => {
    try {
      const { token } = getState().auth;
      const config = {
        headers: {
          Authorization: `Bearer ${token}`
        }
      };
      const response = await axios.get(`${API_URL}/users/me`, config);
      return response.data;
    } catch (error) {
      return rejectWithValue(error.response?.data?.msg || 'Failed to get user data');
    }
  }
);

// Update user profile
export const updateProfile = createAsyncThunk(
  'auth/updateProfile',
  async (userData, { rejectWithValue, getState }) => {
    try {
      const { token } = getState().auth;
      const config = {
        headers: {
          Authorization: `Bearer ${token}`
        }
      };
      const response = await axios.put(`${API_URL}/users/me`, userData, config);
      saveAuthData(null, response.data);
      return response.data;
    } catch (error) {
      return rejectWithValue(error.response?.data?.msg || 'Failed to update profile');
    }
  }
);

// Update password
export const updatePassword = createAsyncThunk(
  'auth/updatePassword',
  async (passwordData, { rejectWithValue, getState }) => {
    try {
      const { token } = getState().auth;
      const config = {
        headers: {
          Authorization: `Bearer ${token}`
        }
      };
      const response = await axios.put(
        `${API_URL}/users/me/password`,
        passwordData,
        config
      );
      return response.data;
    } catch (error) {
      return rejectWithValue(error.response?.data?.msg || 'Failed to update password');
    }
  }
);

const authSlice = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    logout: (state) => {
      clearAuthData();
      state.user = null;
      state.token = null;
      state.isAuthenticated = false;
      state.loading = false;
      state.error = null;
    },
    clearError: (state) => {
      state.error = null;
    }
  },
  extraReducers: (builder) => {
    builder
      // Register
      .addCase(register.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(register.fulfilled, (state, action) => {
        state.loading = false;
        state.isAuthenticated = true;
        state.user = action.payload.user;
        state.token = action.payload.token;
      })
      .addCase(register.rejected, (state, action) => {
        state.loading = false;
        state.error = action.payload;
      })
      // Login
      .addCase(login.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(login.fulfilled, (state, action) => {
        state.loading = false;
        state.isAuthenticated = true;
        state.user = action.payload.user;
        state.token = action.payload.token;
      })
      .addCase(login.rejected, (state, action) => {
        state.loading = false;
        state.error = action.payload;
      })
      // Get current user
      .addCase(getCurrentUser.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(getCurrentUser.fulfilled, (state, action) => {
        state.loading = false;
        state.isAuthenticated = true;
        state.user = action.payload;
      })
      .addCase(getCurrentUser.rejected, (state, action) => {
        state.loading = false;
        state.error = action.payload;
      })
      // Update profile
      .addCase(updateProfile.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(updateProfile.fulfilled, (state, action) => {
        state.loading = false;
        state.user = action.payload;
      })
      .addCase(updateProfile.rejected, (state, action) => {
        state.loading = false;
        state.error = action.payload;
      })
      // Update password
      .addCase(updatePassword.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(updatePassword.fulfilled, (state) => {
        state.loading = false;
      })
      .addCase(updatePassword.rejected, (state, action) => {
        state.loading = false;
        state.error = action.payload;
      });
  }
});

export const { logout, clearError } = authSlice.actions;

export default authSlice.reducer; 