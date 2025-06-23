import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import {
  Box,
  Container,
  Grid,
  Paper,
  Typography,
  Card,
  CardContent,
  CardActions,
  Button,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Chip,
  IconButton,
  Divider,
  List,
  ListItem,
  ListItemText,
  ListItemIcon,
  ListItemSecondaryAction
} from '@mui/material';
import {
  ShoppingCart as CartIcon,
  People as PeopleIcon,
  Inventory as InventoryIcon,
  AttachMoney as MoneyIcon,
  TrendingUp as TrendingUpIcon,
  LocalShipping as ShippingIcon,
  Warning as WarningIcon,
  Visibility as VisibilityIcon,
  Edit as EditIcon,
  Delete as DeleteIcon
} from '@mui/icons-material';
import { fetchOrders } from '../../store/slices/orderSlice';
import { fetchProducts } from '../../store/slices/productSlice';
import { fetchUsers } from '../../store/slices/userSlice';

const Dashboard = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { orders } = useSelector((state) => state.orders);
  const { products } = useSelector((state) => state.products);
  const { users } = useSelector((state) => state.users);

  const [stats, setStats] = useState({
    totalSales: 0,
    totalOrders: 0,
    totalProducts: 0,
    totalUsers: 0,
    lowStockProducts: 0,
    pendingOrders: 0
  });

  useEffect(() => {
    dispatch(fetchOrders());
    dispatch(fetchProducts());
    dispatch(fetchUsers());
  }, [dispatch]);

  useEffect(() => {
    if (orders && products && users) {
      const totalSales = orders.reduce((sum, order) => sum + order.totalAmount, 0);
      const lowStockProducts = products.filter(product => product.stock < 10).length;
      const pendingOrders = orders.filter(order => order.status === 'pending').length;

      setStats({
        totalSales,
        totalOrders: orders.length,
        totalProducts: products.length,
        totalUsers: users.length,
        lowStockProducts,
        pendingOrders
      });
    }
  }, [orders, products, users]);

  const StatCard = ({ title, value, icon, color }) => (
    <Card>
      <CardContent>
        <Box sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
          <Box
            sx={{
              backgroundColor: `${color}.light`,
              borderRadius: '50%',
              p: 1,
              mr: 2
            }}
          >
            {icon}
          </Box>
          <Typography variant="h6" color="text.secondary">
            {title}
          </Typography>
        </Box>
        <Typography variant="h4">
          {typeof value === 'number' ? value.toLocaleString() : value}
        </Typography>
      </CardContent>
    </Card>
  );

  const RecentOrders = () => (
    <Paper sx={{ p: 2 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
        <Typography variant="h6">Recent Orders</Typography>
        <Button
          size="small"
          onClick={() => navigate('/admin/orders')}
        >
          View All
        </Button>
      </Box>
      <TableContainer>
        <Table size="small">
          <TableHead>
            <TableRow>
              <TableCell>Order ID</TableCell>
              <TableCell>Customer</TableCell>
              <TableCell>Amount</TableCell>
              <TableCell>Status</TableCell>
              <TableCell>Actions</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {orders.slice(0, 5).map((order) => (
              <TableRow key={order._id}>
                <TableCell>{order._id}</TableCell>
                <TableCell>{order.shippingInfo.firstName} {order.shippingInfo.lastName}</TableCell>
                <TableCell>${order.totalAmount.toFixed(2)}</TableCell>
                <TableCell>
                  <Chip
                    label={order.status}
                    size="small"
                    color={
                      order.status === 'pending' ? 'warning' :
                      order.status === 'processing' ? 'info' :
                      order.status === 'shipped' ? 'primary' :
                      order.status === 'delivered' ? 'success' : 'error'
                    }
                  />
                </TableCell>
                <TableCell>
                  <IconButton
                    size="small"
                    onClick={() => navigate(`/admin/orders/${order._id}`)}
                  >
                    <VisibilityIcon />
                  </IconButton>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Paper>
  );

  const LowStockProducts = () => (
    <Paper sx={{ p: 2 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
        <Typography variant="h6">Low Stock Products</Typography>
        <Button
          size="small"
          onClick={() => navigate('/admin/products')}
        >
          View All
        </Button>
      </Box>
      <List>
        {products
          .filter(product => product.stock < 10)
          .slice(0, 5)
          .map((product) => (
            <ListItem key={product._id}>
              <ListItemIcon>
                <InventoryIcon color="warning" />
              </ListItemIcon>
              <ListItemText
                primary={product.name}
                secondary={`Stock: ${product.stock} units`}
              />
              <ListItemSecondaryAction>
                <IconButton
                  edge="end"
                  size="small"
                  onClick={() => navigate(`/admin/products/${product._id}`)}
                >
                  <EditIcon />
                </IconButton>
              </ListItemSecondaryAction>
            </ListItem>
          ))}
      </List>
    </Paper>
  );

  const QuickActions = () => (
    <Paper sx={{ p: 2 }}>
      <Typography variant="h6" gutterBottom>
        Quick Actions
      </Typography>
      <Grid container spacing={2}>
        <Grid item xs={12} sm={6}>
          <Button
            fullWidth
            variant="contained"
            startIcon={<InventoryIcon />}
            onClick={() => navigate('/admin/products/new')}
          >
            Add New Product
          </Button>
        </Grid>
        <Grid item xs={12} sm={6}>
          <Button
            fullWidth
            variant="contained"
            startIcon={<PeopleIcon />}
            onClick={() => navigate('/admin/users')}
          >
            Manage Users
          </Button>
        </Grid>
        <Grid item xs={12} sm={6}>
          <Button
            fullWidth
            variant="contained"
            startIcon={<CartIcon />}
            onClick={() => navigate('/admin/orders')}
          >
            View All Orders
          </Button>
        </Grid>
        <Grid item xs={12} sm={6}>
          <Button
            fullWidth
            variant="contained"
            startIcon={<TrendingUpIcon />}
            onClick={() => navigate('/admin/reports')}
          >
            View Reports
          </Button>
        </Grid>
      </Grid>
    </Paper>
  );

  return (
    <Container maxWidth="lg">
      <Box sx={{ mt: 4, mb: 4 }}>
        <Typography variant="h4" gutterBottom>
          Admin Dashboard
        </Typography>

        <Grid container spacing={3}>
          {/* Stats Cards */}
          <Grid item xs={12} sm={6} md={3}>
            <StatCard
              title="Total Sales"
              value={`$${stats.totalSales.toFixed(2)}`}
              icon={<MoneyIcon color="primary" />}
              color="primary"
            />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <StatCard
              title="Total Orders"
              value={stats.totalOrders}
              icon={<CartIcon color="success" />}
              color="success"
            />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <StatCard
              title="Total Products"
              value={stats.totalProducts}
              icon={<InventoryIcon color="info" />}
              color="info"
            />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <StatCard
              title="Total Users"
              value={stats.totalUsers}
              icon={<PeopleIcon color="warning" />}
              color="warning"
            />
          </Grid>

          {/* Recent Orders */}
          <Grid item xs={12} md={8}>
            <RecentOrders />
          </Grid>

          {/* Quick Actions */}
          <Grid item xs={12} md={4}>
            <QuickActions />
          </Grid>

          {/* Low Stock Products */}
          <Grid item xs={12}>
            <LowStockProducts />
          </Grid>
        </Grid>
      </Box>
    </Container>
  );
};

export default Dashboard; 