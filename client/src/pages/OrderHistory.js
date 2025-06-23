import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import {
  Box,
  Container,
  Typography,
  Paper,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Chip,
  IconButton,
  Button,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Grid,
  Divider,
  Card,
  CardMedia,
  CardContent,
  CardActions,
  Pagination,
  TextField,
  InputAdornment
} from '@mui/material';
import {
  Visibility as VisibilityIcon,
  Search as SearchIcon,
  LocalShipping as ShippingIcon,
  Payment as PaymentIcon,
  Receipt as ReceiptIcon
} from '@mui/icons-material';
import { fetchUserOrders } from '../store/slices/orderSlice';

const getStatusColor = (status) => {
  switch (status.toLowerCase()) {
    case 'pending':
      return 'warning';
    case 'processing':
      return 'info';
    case 'shipped':
      return 'primary';
    case 'delivered':
      return 'success';
    case 'cancelled':
      return 'error';
    default:
      return 'default';
  }
};

const OrderHistory = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { userOrders, loading } = useSelector((state) => state.orders);
  const [selectedOrder, setSelectedOrder] = useState(null);
  const [showOrderDetails, setShowOrderDetails] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [page, setPage] = useState(1);
  const ordersPerPage = 5;

  useEffect(() => {
    dispatch(fetchUserOrders());
  }, [dispatch]);

  const handleViewOrder = (order) => {
    setSelectedOrder(order);
    setShowOrderDetails(true);
  };

  const handlePageChange = (event, value) => {
    setPage(value);
  };

  const filteredOrders = userOrders.filter(order => 
    order._id.toLowerCase().includes(searchQuery.toLowerCase()) ||
    order.status.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const paginatedOrders = filteredOrders.slice(
    (page - 1) * ordersPerPage,
    page * ordersPerPage
  );

  const OrderDetailsDialog = () => (
    <Dialog
      open={showOrderDetails}
      onClose={() => setShowOrderDetails(false)}
      maxWidth="md"
      fullWidth
    >
      <DialogTitle>
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
          <ReceiptIcon />
          Order Details
        </Box>
      </DialogTitle>
      <DialogContent>
        {selectedOrder && (
          <Box>
            <Grid container spacing={3}>
              <Grid item xs={12} md={6}>
                <Typography variant="subtitle1" gutterBottom>
                  Order Information
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  Order ID: {selectedOrder._id}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  Date: {new Date(selectedOrder.createdAt).toLocaleDateString()}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  Status: 
                  <Chip
                    label={selectedOrder.status}
                    color={getStatusColor(selectedOrder.status)}
                    size="small"
                    sx={{ ml: 1 }}
                  />
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  Payment Method: {selectedOrder.paymentMethod}
                </Typography>
              </Grid>
              <Grid item xs={12} md={6}>
                <Typography variant="subtitle1" gutterBottom>
                  Shipping Information
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  {selectedOrder.shippingInfo.firstName} {selectedOrder.shippingInfo.lastName}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  {selectedOrder.shippingInfo.address}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  {selectedOrder.shippingInfo.city}, {selectedOrder.shippingInfo.postalCode}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  {selectedOrder.shippingInfo.country}
                </Typography>
              </Grid>
            </Grid>

            <Divider sx={{ my: 3 }} />

            <Typography variant="subtitle1" gutterBottom>
              Order Items
            </Typography>
            <Grid container spacing={2}>
              {selectedOrder.items.map((item) => (
                <Grid item xs={12} key={item._id}>
                  <Card sx={{ display: 'flex' }}>
                    <CardMedia
                      component="img"
                      image={item.product.images[0]}
                      alt={item.product.name}
                      sx={{ width: 100, height: 100, objectFit: 'cover' }}
                    />
                    <Box sx={{ display: 'flex', flexDirection: 'column', flexGrow: 1 }}>
                      <CardContent>
                        <Typography variant="subtitle1">
                          {item.product.name}
                        </Typography>
                        <Typography variant="body2" color="text.secondary">
                          Quantity: {item.quantity}
                        </Typography>
                        <Typography variant="body2" color="text.secondary">
                          Price: ${item.price}
                        </Typography>
                      </CardContent>
                    </Box>
                    <Box sx={{ display: 'flex', alignItems: 'center', p: 2 }}>
                      <Typography variant="subtitle1">
                        ${(item.price * item.quantity).toFixed(2)}
                      </Typography>
                    </Box>
                  </Card>
                </Grid>
              ))}
            </Grid>

            <Divider sx={{ my: 3 }} />

            <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 2 }}>
              <Typography>Subtotal</Typography>
              <Typography>${selectedOrder.totalAmount.toFixed(2)}</Typography>
            </Box>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 2 }}>
              <Typography>Shipping</Typography>
              <Typography>
                {selectedOrder.shippingCost === 0 ? 'Free' : `$${selectedOrder.shippingCost.toFixed(2)}`}
              </Typography>
            </Box>
            <Divider sx={{ my: 2 }} />
            <Box sx={{ display: 'flex', justifyContent: 'space-between' }}>
              <Typography variant="h6">Total</Typography>
              <Typography variant="h6">
                ${(selectedOrder.totalAmount + selectedOrder.shippingCost).toFixed(2)}
              </Typography>
            </Box>
          </Box>
        )}
      </DialogContent>
      <DialogActions>
        <Button onClick={() => setShowOrderDetails(false)}>
          Close
        </Button>
      </DialogActions>
    </Dialog>
  );

  if (loading) {
    return (
      <Container>
        <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '60vh' }}>
          <Typography>Loading...</Typography>
        </Box>
      </Container>
    );
  }

  return (
    <Container maxWidth="lg">
      <Box sx={{ mt: 4, mb: 4 }}>
        <Typography variant="h4" gutterBottom>
          Order History
        </Typography>

        <Box sx={{ mb: 3 }}>
          <TextField
            fullWidth
            placeholder="Search orders by ID or status..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            InputProps={{
              startAdornment: (
                <InputAdornment position="start">
                  <SearchIcon />
                </InputAdornment>
              ),
            }}
          />
        </Box>

        <TableContainer component={Paper}>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>Order ID</TableCell>
                <TableCell>Date</TableCell>
                <TableCell>Status</TableCell>
                <TableCell>Total</TableCell>
                <TableCell>Payment Method</TableCell>
                <TableCell>Actions</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {paginatedOrders.map((order) => (
                <TableRow key={order._id}>
                  <TableCell>{order._id}</TableCell>
                  <TableCell>
                    {new Date(order.createdAt).toLocaleDateString()}
                  </TableCell>
                  <TableCell>
                    <Chip
                      label={order.status}
                      color={getStatusColor(order.status)}
                      size="small"
                    />
                  </TableCell>
                  <TableCell>${order.totalAmount.toFixed(2)}</TableCell>
                  <TableCell>{order.paymentMethod}</TableCell>
                  <TableCell>
                    <IconButton
                      size="small"
                      onClick={() => handleViewOrder(order)}
                    >
                      <VisibilityIcon />
                    </IconButton>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>

        <Box sx={{ display: 'flex', justifyContent: 'center', mt: 3 }}>
          <Pagination
            count={Math.ceil(filteredOrders.length / ordersPerPage)}
            page={page}
            onChange={handlePageChange}
            color="primary"
          />
        </Box>
      </Box>

      <OrderDetailsDialog />
    </Container>
  );
};

export default OrderHistory; 